<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\commands\EggWarsCommand;
use eggwars\EggWars;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class StartSubcommand
 * @package eggwars\commands\subcommands
 */
class StartSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * StartSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$sender instanceof Player) {
            $sender->sendMessage(EggWars::getPrefix()."§cThis command can be used only in-game!");
            return;
        }
        if(!$this->checkPermission($sender, $name)) return;
    }
}