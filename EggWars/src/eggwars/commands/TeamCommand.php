<?php

declare(strict_types=1);

namespace eggwars\commands;

use eggwars\arena\Arena;
use eggwars\EggWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class TeamCommand
 * @package eggwars\commands
 */
class TeamCommand extends Command implements PluginIdentifiableCommand {

    /**
     * TeamCommand constructor.
     */
    public function __construct() {
        parent::__construct("team", "Select your team", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return false;
        }
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/team <team>");
            return false;
        }
        $arena = $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender);
        if(!$arena instanceof Arena) {
            $sender->sendMessage("§cJoin EggWars game to use this command!");
            return false;
        }
        $arena->addPlayerToTeam($sender, $args[0]);
        return false;
    }

    /**
     * @return EggWars|Plugin $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}