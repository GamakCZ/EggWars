<?php

/*
 *    _____                  __        __
 *   | ____|   __ _    __ _  \ \      / /   __ _   _ __   ___
 *   |  _|    / _` |  / _` |  \ \ /\ / /   / _` | | '__| / __|
 *   | |___  | (_| | | (_| |   \ V  V /   | (_| | | |    \__ \
 *   |_____|  \__, |  \__, |    \_/\_/     \__,_| |_|    |___/
 *           |___/   |___/
 */

declare(strict_types=1);

namespace eggwars\event\listener;

use eggwars\arena\Arena;
use eggwars\EggWars;
use eggwars\position\EggWarsVector;
use eggwars\utils\Color;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\Server;

/**
 * Class ArenaSetupManager
 * @package eggwars
 */
class ArenaSetupManager implements Listener {

    /** @var Arena[] */
    public static $players = [];

    /** @var array $b */
    private $b = [];

    /**
     * ArenaSetupManager constructor.
     */
    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, EggWars::getInstance());
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event) {

        $player = $event->getPlayer();

        if (empty(self::$players[$player->getName()])) {
            return;
        }
        $arena = self::$players[$player->getName()];
        $args = explode(" ", $event->getMessage());
        if (empty($args[0])) {
            $player->sendMessage("§7Use §6help §7 to display setup commands!");
            $event->setCancelled(true);
            return;
        }
        switch (strtolower($args[0])) {
            case "help":
                if (isset($args[1]) && $args[1] == "2") {
                    $player->sendMessage("§9--- §6§lEggWars setup help§l 2/2§9 ---§r§f\n" .
                        "§2enable §bEnable arena\n" .
                        "§2joinsign §bSet join sign\n" .
                        "§2gametime §bSet max gametime\n" .
                        "§2starttime §bSet start time\n" .
                        "§2teamstostart §bSet count of teams to start\n");
                    break;
                }
                $player->sendMessage("§9--- §6§lEggWars setup help§l 1/2§9 ---§r§f\n" .
                    "§2players §bSet players per team count\n" .
                    "§2addteam §bAdd new team\n" .
                    "§2setteam §bSet team\n" .
                    "§2delteam §bRemove team\n" .
                    "§2teams §bDisplays list of teams\n" .
                    "§2lobby §bSet waiting lobby\n" .
                    "§2help §bDisplays help\n");
                break;
            case "players":
                if (empty($args[1])) {
                    $player->sendMessage("§cUsage: §7players <count>");
                    break;
                }
                if (!is_numeric($args[1])) {
                    $player->sendMessage("§cCount must be numeric!");
                    break;
                }
                $arena->arenaData["playersPerTeam"] = intval($args[1]);
                $player->sendMessage("§aPlayers per team updated!");
                break;
            case "addteam":
                if (count($args) < 3) {
                    $player->sendMessage("§cUsage: §7addteam <teamName> <teamColor: &(color: a-f|0-9)>");
                    break;
                }
                if (isset($arena->arenaData["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam {$args[1]} already exists!");
                    break;
                }
                if(!Color::mcColorExists(str_replace("&", "§", $args[2]))) {
                    $player->sendMessage("§cColor {$args[1]} does not found!");
                    break;
                }
                $arena->arenaData["teams"][$args[1]] = [
                    "color" => $color = str_replace("&", "§", $args[2]),
                    "spawn" => []
                ];
                $player->sendMessage("§aTeam {$color}{$args[1]} added.");
                break;
            case "setteam":
                if (count($args) < 3) {
                    $player->sendMessage("§cUsage: §7setteam <team> <color> [data]");
                    break;
                }
                if (empty($arena->arenaData["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam $args[1] does not found!");
                    break;
                }
                switch ($args[2]) {
                    case "color":
                        if (empty($args[3]) || empty($args[4]) || strlen($args[4]) !== 2) {
                            $player->sendMessage("§cUsage: §7setteam color <teamColor: &(color: a-f|0-9)>");
                            break;
                        }
                        $arena->arenaData["teams"][$args[3]]["color"] = $c = str_replace("&", "§", $args[4]);
                        $player->sendMessage("§aTeam color was changed to $c" . Color::getColorNameFormMC($c) . "§a.");
                        break;
                    default:
                        $player->sendMessage("§cUsage: §7setteam color");
                        break;
                }
                break;
            case "delteam":
                if (empty($args[1])) {
                    $player->sendMessage("§cUsage: §7delteam <team>");
                    break;
                }
                if (empty($arena->arenaData["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam $args[1] does not found.");
                    break;
                }
                unset($arena->arenaData["teams"][$args[1]]);
                $player->sendMessage("§aTeam $args[1] removed!");
                break;
            case "lobby":
                $arena->arenaData["lobby"] = [$player->getX(), $player->getY(), $player->getZ(), $player->getLevel()->getFolderName()];
                $player->sendMessage("§aArena {$arena->getName()} lobby updated!");
                break;
            case "done":
                unset(self::$players[$player->getName()]);
                $player->sendMessage("§aYou are leaved setup mode.");
                break;
            case "enable":
                $arena->arenaData["enabled"] = true;
                $player->sendMessage("§aArena enabled!");
                break;
            case "teams":
                $teams = [];
                foreach ($arena->arenaData["teams"] as $team => $teamData) {
                    array_push($teams, $teamData["color"] . $team);
                }
                if(count($teams) == 0) {
                    $player->sendMessage("§cThere are no teams.");
                    break;
                }
                $player->sendMessage("§aTeams (".count($teams)."): " . implode(", ", $teams) . ".");
                break;
            case "joinsign":
                $player->sendMessage("§aDestroy the block to set joinsign!");
                $this->b[$player->getName()] = [0, "sign"];
                break;
            case "gametime":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7gametime <time>");
                    break;
                }
                if(!is_numeric($args[1])) {
                    $player->sendMessage("§cTime must be numeric!");
                    break;
                }
                $arena->arenaData["gametime"] = intval($args[1]);
                $player->sendMessage("§aGametime updated!");
                break;
            case "starttime":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7starttime <time>");
                    break;
                }
                if(!is_numeric($args[1])) {
                    $player->sendMessage("§cTime must be numeric!");
                    break;
                }
                $arena->arenaData["starttime"] = intval($args[1]);
                $player->sendMessage("§aGametime updated!");
                break;
            default:
                $player->sendMessage("§aUse §chelp §afor help, §cdone §afor exit!");
                break;
        }
        $event->setCancelled(true);

    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        if(isset($this->b[$event->getPlayer()->getName()])) {
            $arena = self::$players[$event->getPlayer()->getName()];
            switch ($this->b[$event->getPlayer()->getName()][0]) {
                case 0:
                    $arena->arenaData[$this->b[$event->getPlayer()->getName()][1]] = [$event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ(), $event->getBlock()->getLevel()->getFolderName()];
                    $event->getPlayer()->sendMessage("§aJoin sign updated!");
                    $event->setCancelled(true);
                    unset($this->b[$event->getPlayer()->getName()]);
                    break;
            }
        }
    }

    /**
     * @param Player $player
     * @param Arena $arena
     */
    public static function addPlayer(Player $player, Arena $arena) {
        if(isset(LevelSetupManager::$players[$player->getName()])) {
            $player->sendMessage("§cLeave LevelSetupMode first!");
            return;
        }
        if(empty(self::$players[$player->getName()])) {
            self::$players[$player->getName()] = $arena;
            $player->sendMessage("§aYou are now in setup system. Type §chelp §afor help, §cdone §afor exit.");
        }
        else {
            $player->sendMessage("§cYou are already in setup mode!");
        }
    }
}