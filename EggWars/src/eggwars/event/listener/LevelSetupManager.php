<?php

declare(strict_types=1);

namespace eggwars\event\listener;

use eggwars\EggWars;
use eggwars\level\EggWarsLevel;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\Server;

/**
 * Class ArenaSetupManager
 * @package eggwars
 */
class LevelSetupManager implements Listener {

    /** @var EggWarsLevel[] */
    private static $players = [];


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
                break;
        }
        $event->setCancelled(true);
    }

    /**
     * @param Player $player
     * @param EggWarsLevel $level
     */
    public static function addPlayer(Player $player, EggWarsLevel $level) {
        self::$players[$player->getName()] = $level;
        $player->sendMessage("§aYou are now in setup system. Type §chelp §afor help.");
    }
}