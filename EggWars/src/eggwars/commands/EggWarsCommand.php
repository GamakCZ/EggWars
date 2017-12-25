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
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew join");
            return;
        }
        switch (strtolower($args[0])) {
            case "join":
                $this->getPlugin()->getArenaManager()->getArenaByName("TestArena")->joinPlayer($sender);
                return;
            default:
                $sender->sendMessage("§cUsage: §7/ew join");
                return;
        }
    }

    /**
     * @return Plugin|EggWars $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}