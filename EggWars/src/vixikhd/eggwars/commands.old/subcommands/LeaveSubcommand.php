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

namespace vixikhd\eggwars\commands\subcommands;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\commands\EggWarsCommand;
use vixikhd\eggwars\EggWars;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class LeaveSubcommand
 * @package eggwars\commands\subcommands
 */
class LeaveSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * LeaveSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$this->checkPermission($sender, $name)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage(EggWars::getPrefix()."§cThis command can be used only in-game!");
            return;
        }
        if(!$this->getPlugin()->getArenaManager()->getArenaByPlayer($sender) instanceof Arena) {
            $sender->sendMessage(EggWars::getPrefix()."§cThis command can be used only in-arena");
            return;
        }
        $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender)->disconnectPlayer($sender);
    }
}