<?php

declare(strict_types=1);

namespace eggwars\commands;

use eggwars\EggWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class EggWarsCommand
 * @package eggwars\commands
 */
class EggWarsCommand extends Command implements PluginIdentifiableCommand {

    public function __construct() {
        parent::__construct("eggwars", "EggWars commands", null, ["ew"]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) return;
        if(!$sender->hasPermission("ew.cmd")) return;
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew help");
            return;
        }
        switch (strtolower($args[0])) {
            case "help":
                $sender->sendMessage("§a-- EggWars Help --\n".
                "§6/ew create§e create arena\n".
                "§6/ew set§e set arena");
                return;
            case "create":
                if(empty($args[1])) {
                    $sender->sendMessage("§cUsage: §7/ew create <arenaName>");
                    return;
                }


        }
    }

    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}