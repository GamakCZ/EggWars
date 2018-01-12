<?php

declare(strict_types=1);

namespace eggwars\event\listener;

use eggwars\EggWars;
use eggwars\level\EggWarsLevel;
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
class LevelSetupManager implements Listener {

    /** @var EggWarsLevel[] $players */
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
        if(empty(self::$players[$player->getName()])) {
            return;
        }

        /** @var EggWarsLevel $level */
        $level = self::$players[$player->getName()];
        $args = explode(" ", $event->getMessage());
        if (empty($args[0])) {
            $player->sendMessage("§7Use §6help §7 to display setup commands!");
            $event->setCancelled(true);
            return;
        }
        switch (strtolower($args[0])) {
            case "help":
                $player->sendMessage("§aEggWars Level Setup Help:\n" .
                    "§2addarena §6Add the arena, in there level can be used\n" .
                    "§2setspawn §6Set the team spawn\n".
                    "§2setegg §6Set the team egg");
                break;
            case "addarena":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7addarena <arena>");
                    break;
                }
                if(!EggWars::getInstance()->getArenaManager()->arenaExists($args[1])) {
                    $player->sendMessage("§cArena $args[1] does not found!");
                    break;
                }
                $arena = EggWars::getInstance()->getArenaManager()->getArenaByName($args[1]);
                if(count($arena->arenaData["teams"]) != $level->getTeamsCount()) {
                    $player->sendMessage("§cCount of teams are not equals.");
                    break;
                }
                array_push($level->data["arenas"], $args[1]);
                $player->sendMessage("§aArena $args[1] added!");
                break;
            case "setspawn":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7setspawn <team>");
                    break;
                }
                /*if(empty($level->data["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam $args[1] does not found!");
                    break;
                }*/
                $level->setSpawnVector($args[1], $player->asVector3());
                $player->sendMessage("§aTeam spawn updated!");
                break;
            case "setegg":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7setegg <team>");
                    break;
                }
                /*if(empty($level->data["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam $args[1] does not found!");
                    break;
                }*/
                $this->b[$player->getName()] = [0, $args[1], $level];
                $player->sendMessage("§aDestroy the egg to update it!");
                break;
            case "done":
                unset(self::$players[$player->getName()]);
                $player->sendMessage("§aYou are leaved setup mode.");
                break;
            default:
                $player->sendMessage("§aType §chelp §afor help, §cdone §afor exit.");
        }
        $event->setCancelled(true);
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        if(isset($this->b[$event->getPlayer()->getName()])) {
            $d = $this->b[$event->getPlayer()->getName()];
            /** @var EggWarsLevel $level */
            $level = $d[2];
            switch ($d[0]) {
                case 0:
                    $level->setEggVector($d[1], $event->getBlock()->asVector3());
                    $event->getPlayer()->sendMessage("§a$d[1] team egg updated!");
                    unset($this->b[$event->getPlayer()->getName()]);
                    $event->setCancelled(true);
                    break;
            }
        }
    }

    /**
     * @param Player $player
     * @param EggWarsLevel $level
     */
    public static function addPlayer(Player $player, EggWarsLevel $level) {
        if(empty(self::$players[$player->getName()])) {
            self::$players[$player->getName()] = $level;
            $player->sendMessage("§aYou are now in setup system. Type §chelp §afor help, §cdone §afor exit.");
        }
        else {
            $player->sendMessage("§cYou are already in setup mode.");
        }
    }

    public function getPlugin() {
        return EggWars::getInstance();
    }
}