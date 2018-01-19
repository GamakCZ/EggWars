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

namespace eggwars\commands\subcommands;

use eggwars\commands\EggWarsCommand;
use eggwars\EggWars;
use eggwars\event\listener\ArenaSetupManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class SetSubcommand
 * @package eggwars\commands\subcommands
 */
class SetSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * SetSubcommand constructor.
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
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew set <arena>");
            return;
        }
        if(!$this->getPlugin()->getArenaManager()->arenaExists($args[0])) {
            $sender->sendMessage(EggWars::getPrefix()."§cArena $args[0] does not found!");
            return;
        }
        ArenaSetupManager::addPlayer($sender, $this->getPlugin()->getArenaManager()->getArenaByName($args[0]));
    }
}