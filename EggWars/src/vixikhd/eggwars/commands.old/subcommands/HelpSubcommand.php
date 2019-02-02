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

use vixikhd\eggwars\commands\EggWarsCommand;
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
        $msg = "§9--- §c§lEggWars help§l§9 ---§r§f\n";
        if($this->checkPermission($sender, "help")) $msg .= "§2/ew help §fDisplays all EggWars commands\n";
        if($this->checkPermission($sender, "create")) $msg .= "§2/ew create §fCreate new arena\n";
        if($this->checkPermission($sender, "set")) $msg .= "§2/ew set §fSet arena\n";
        if($this->checkPermission($sender, "delete")) $msg .= "§2/ew delete §fDelete arena\n";
        if($this->checkPermission($sender, "arenas")) $msg .= "§2/ew arenas §fDisplays list arenas\n";
        if($this->checkPermission($sender, "level")) $msg .= "§2/ew leavel §fManage levels";
        $sender->sendMessage($msg);
    }
}