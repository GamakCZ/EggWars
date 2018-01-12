<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\commands\EggWarsCommand;
use eggwars\EggWars;
use pocketmine\command\CommandSender;

/**
 * Class DeleteSubcommand
 * @package eggwars\commands\subcommands
 */
class DeleteSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * DeleteSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$this->checkPermission($sender, $name)) return;
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew delete <arenaName>");
            return;
        }
        if(!$this->getPlugin()->getArenaManager()->arenaExists($args[0])) {
            $sender->sendMessage(EggWars::getPrefix()."§cArena $args[0] does not found!");
            return;
        }
        $this->getPlugin()->getArenaManager()->removeArena($name);
        $sender->sendMessage(EggWars::getPrefix()."§aArena $args[0] sucessfully removed!");
    }
}