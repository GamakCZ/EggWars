<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\arena\Arena;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\Server;

class SetupManager implements Listener {

    /** @var Arena[] */
    private static $players = [];

    /** @var array $setup */
    private $setup = [];

    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents($this, EggWars::getInstance());
    }

    public function onChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();
        $arena = self::$players[strtolower($player->getName())];
        if (isset(self::$players[strtolower($player->getName())]) && $arena instanceof Arena) {
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
                case "addteam":
                    if(count($args) < 3) {
                        $player->sendMessage("§cUsage: §7addteam <teamName> <teamColor: &(color:a-f|0-9)>");
                        break;
                    }
                    if(isset($arena["teams"][$args[1]])) {
                        $player->sendMessage("§cTeam {$args[1]} already exists!");
                        break;
                    }
                    $arena["teams"][$args[1]] = [
                        "color" => str_replace("&", "§", $args[2]),
                        "spawn" => []
                    ];
                    $player->sendMessage("§aTeam {$args[1]} added (color $args[2]).");
                    break;
                case "setspawn":
                    if(empty($args[1])) {
                        $player->sendMessage("§cUsage: §7setspawn <teamName>");
                        break;
                    }
            }
            $event->setCancelled(true);
        }
    }

    public static function addPlayer(Player $player, Arena $arena) {
        self::$players[strtolower($player->getName())] = $arena;
    }
}