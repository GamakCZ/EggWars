<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

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