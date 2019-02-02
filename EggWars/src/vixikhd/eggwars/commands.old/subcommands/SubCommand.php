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

use pocketmine\command\CommandSender;

/**
 * Interface SubCommand
 * @package eggwars\commands\subcommands
 */
interface SubCommand {

    /**
     * SubCommand constructor.
     */
    public function __construct();

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return void
     */
    public function executeSub(CommandSender $sender, array $args, string $name);
}