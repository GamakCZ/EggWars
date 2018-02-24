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
                $player->sendMessage("§9--- §6§lEggWars level setup help§l 1/1§r§9 ---§r§f\n" .
                    "§2set §bSet the arena, in there level can be used\n" .
                    "§2spawn §bSet the team spawn\n".
                    "§2egg §bSet the team egg");
                break;
            case "set":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7add <arena>");
                    break;
                }
                if(!EggWars::getInstance()->getArenaManager()->arenaExists($args[1])) {
                    $player->sendMessage("§cArena $args[1] does not found!");
                    break;
                }
                if(in_array($args[1], $level->data["arenas"])) {
                    $player->sendMessage("§cArena is already set!");
                    break;
                }
                $arena = EggWars::getInstance()->getArenaManager()->getArenaByName($args[1]);
                array_push($level->data["arenas"], $args[1]);
                $player->sendMessage("§aArena $args[1] set! §bImporting teams...");
                foreach($arena->arenaData["teams"] as $team => ["color" => $color]) {
                    $player->sendMessage("§a{$color}{$team} team imported!");
                    $level->data["teams"][$team] = [
                        "spawn" => [],
                        "egg" => []
                    ];
                }
                break;
            case "spawn":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7spawn <team>");
                    break;
                }
                if(empty($level->data["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam {$args[1]} does not found, you can import it adding new arena!");
                    break;
                }
                $level->setSpawnVector($args[1], $player->asVector3());
                $player->sendMessage("§a{$args[1]} §ateam spawn updated!");
                break;
            case "egg":
                if(empty($args[1])) {
                    $player->sendMessage("§cUsage: §7egg <team>");
                    break;
                }
                if(empty($level->data["teams"][$args[1]])) {
                    $player->sendMessage("§cTeam {$args[1]} does not found, you can import it adding new arena!");
                    break;
                }
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
        if(isset(LevelSetupManager::$players[$player->getName()])) {
            $player->sendMessage("§cLeave ArenaSetupMode first!");
            return;
        }
        if(empty(self::$players[$player->getName()])) {
            self::$players[$player->getName()] = $level;
            $player->sendMessage("§aYou are now in setup system. Type §chelp §afor help, §cdone §afor exit.");
        }
        else {
            $player->sendMessage("§cYou are already in setup mode.");
        }
    }

    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }
}