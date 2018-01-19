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
use pocketmine\command\CommandSender;

/**
 * Class HelpSubcommand
 * @package eggwars\commands\subcommands
 */
class HelpSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * HelpSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$this->checkPermission($sender, $name)) return;
        $sender->sendMessage("§7--- == §8[ §6EggWars §8] §7== ---\n".
        "§9/ew help §7Displays all EggWars commands\n".
        "§9/ew create §7Create new arena\n".
        "§9/ew delete §7Delete arena\n".
        "§9/ew arenas §7Displays list arenas\n".
        "§9/ew level §7EggWars level manager\n".
        "§9/ew set §7Set arena");
    }
}