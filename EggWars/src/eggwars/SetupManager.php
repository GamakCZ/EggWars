<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\arena\Arena;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;

class SetupManager implements Listener {

    /** @var Arena[] */
    private static $players = [];

    public function onChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();
        if (isset(self::$players[strtolower($player->getName())]) && ($arena = self::$players[strtolower($player->getName())]) instanceof Arena) {
            $args = explode(" ", $event->getMessage());
            if (empty($args[0])) {
                $player->sendMessage("§7Use §6help §7 to display setup commands!");
                $event->setCancelled(true);
                return;
            }
            switch (strtolower($args[0])) {
                case "help":
                    $player->sendMessage("§aEggWars Setup Help:\n" .
                        "§2playersperteam §6Set players per team count\n" .
                        "§2addteam §6Add new team\n" .
                        "§2setspawn §6Set team spawn\n" .
                        "§2setegg §6Set team egg\n" .
                        "§2setlobby §6Set waiting lobby\n" .
                        "§2setsign §6Set spawning sign\n" .
                        "§2setupinfo §6Displays setuped info\n" .
                        "§2settime §6Set game times\n" .
                        "§2setshopsign §6Set the shop sign");
                    break;
                case "playersperteam":
                    if (empty($args[0])) {
                        $player->sendMessage("§cUsage: §7playersperteam <count>");
                        break;
                    }
                    if(!is_numeric($args[0])) {
                        $player->sendMessage("§cCount must be numeric!");
                        break;
                    }
                    $arena->arenaData["playersperteam"] = intval($args[0]);
                    $player->sendMessage("§aArena updated!");
                    break;

            }
            $event->setCancelled(true);
        }
    }

    public static function addPlayer(Player $player, Arena $arena) {
        self::$players[strtolower($player->getName())] = $arena;
    }
}