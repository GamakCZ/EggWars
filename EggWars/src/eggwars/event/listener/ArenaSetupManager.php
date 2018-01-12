<?php

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
    private static $players = [];

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


        if (isset(self::$players[$player->getName()])) {
            $arena = self::$players[$player->getName()];
            $args = explode(" ", $event->getMessage());
            if (empty($args[0])) {
                $player->sendMessage("§7Use §6help §7 to display setup commands!");
                $event->setCancelled(true);
                return;
            }
            switch (strtolower($args[0])) {
                case "help":
                    $player->sendMessage("§aEggWars Arena Setup Help:\n" .
                        "§2playersperteam §6Set players per team count\n" .
                        "§2addteam §6Add new team\n" .
                        "§2setteam §6Set team\n" .
                        "§2delteam §6Remove team\n".
                        "§2teams §6Displays list of teams\n".
                        "§2setlobby §6Set waiting lobby\n" .
                        "§2help §6Displays help\n" .
                        "§2enable §6Enable arena\n".
                        "§2setjoinsign §6Set join sign");
                    break;
                case "playersperteam":
                    if (empty($args[1])) {
                        $player->sendMessage("§cUsage: §7playersperteam <count>");
                        break;
                    }
                    if(!is_numeric($args[1])) {
                        $player->sendMessage("§cCount must be numeric!");
                        break;
                    }
                    $arena->arenaData["playersperteam"] = intval($args[0]);
                    $player->sendMessage("§aPlayers per team updated!");
                    break;
                case "addteam":
                    if(count($args) < 3) {
                        $player->sendMessage("§cUsage: §7addteam <teamName> <teamColor: &(color: a-f|0-9)>");
                        break;
                    }
                    if(isset($arena->arenaData["teams"][$args[1]])) {
                        $player->sendMessage("§cTeam {$args[1]} already exists!");
                        break;
                    }
                    $arena->arenaData["teams"][$args[1]] = [
                        "color" => str_replace("&", "§", $args[2]),
                        "spawn" => []
                    ];
                    $player->sendMessage("§aTeam {$args[1]} added (color $args[2]).");
                    break;
                case "setteam":
                    if(count($args) < 3) {
                        $player->sendMessage("§cUsage: §7setteam <team> <color> [data]");
                        break;
                    }
                    if(empty($arena->arenaData["teams"][$args[1]])) {
                        $player->sendMessage("§cTeam $args[1] does not found!");
                        break;
                    }
                    switch ($args[2]) {
                        case "color":
                            if(empty($args[3]) || strlen($args[3]) !== 2) {
                                $player->sendMessage("§cUsage: §7setteam color <teamColor: &(color: a-f|0-9)>");
                                break;
                            }
                            $arena->arenaData["teams"][$args[2]]["color"] = $c = str_replace("&", "§", $args[3]);
                            $player->sendMessage("§aTeam color was changed to $c".Color::getColorNameFormMC($c)."§a.");
                            break;
                        /*case "spawn":
                            $arena->arenaData["teams"][$args[2]]["spawn"] = [$player->getX(), $player->getY(), $player->getZ()];
                            $player->sendMessage("§aSpawn updated!");
                            break;
                        case "egg":
                            $player->sendMessage("Destroy the egg setting block.");
                            $this->b[$player->getName()] = [0, $args[2]];
                            break;*/
                    }
                    break;
                case "delteam":
                    if(empty($args[1])) {
                        $player->sendMessage("§cUsage: §7delteam <team>");
                        break;
                    }
                    if(empty($arena->arenaData["teams"][$args[1]])) {
                        $player->sendMessage("§cTeam $args[1] does not found.");
                        break;
                    }
                    unset($arena->arenaData["teams"][$args[1]]);
                    $player->sendMessage("§aTeam $args[1] removed!");
                    break;
                case "setlobby":
                    $arena->arenaData["lobby"] = [$player->getX(), $player->getY(), $player->getZ(), $player->getLevel()->getName()];
                    $player->sendMessage("§aArena {$arena->getName()} lobby updated!");
                    break;
                case "done":
                    unset(self::$players[$player->getName()]);
                    $player->sendMessage("§aYou are leaved setup mode.");
                    break;
                case "enable":
                    $arena->arenaData["enable"] = true;
                    $player->sendMessage("§aArena enabled!");
                    break;
                case "teams":
                    $teams = [];
                    foreach ($arena->arenaData["teams"] as $team => $teamData) {
                        array_push($teams, $teamData["color"].$team);
                    }
                    $player->sendMessage("§aTeams: ".implode(", ",$teams).".");
                    break;
                case "setjoinsign":
                    $player->sendMessage("§aDestroy the block to set joinsign!");
                    $this->b[$player->getName()] = [0, "sign"];
                    break;
                default:
                    $player->sendMessage("§aUse §chelp §afor help!");
                    break;
            }
            $event->setCancelled(true);
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        if(isset($this->b[$event->getPlayer()->getName()])) {
            $arena = self::$players[$event->getPlayer()->getName()];
            switch ($this->b[$event->getPlayer()->getName()][0]) {
                case 0:
                    $arena->arenaData[$this->b[$event->getPlayer()->getName()][1]] = [$event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ(), $event->getBlock()->getLevel()->getName()];
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
        if(empty(self::$players[$player->getName()])) {
            self::$players[$player->getName()] = $arena;
            $player->sendMessage("§aYou are now in setup system. Type §chelp §afor help, §cdone §afor exit.");
        }
        else {
            $player->sendMessage("§cYou are already in setup mode!");
        }
    }
}