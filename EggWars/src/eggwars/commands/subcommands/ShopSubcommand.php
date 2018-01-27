<?php

/*
 *    _____                __        __
 *   | ____|  __ _    __ _ \ \      / /__ _  _ __  ___
 *   |  _|   / _` | / _` |  \ \ /\ / // _` || '__|/ __|
 *   | |___ | (_| || (_| |   \ V  V /| (_| || |   \__ \
 *   |_____| \__, | \__, |    \_/\_/  \__,_||_|   |___/
 *           |___/  |___/
 */

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\arena\Arena;
use eggwars\commands\EggWarsCommand;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class ShopSubcommand
 * @package eggwars\commands\subcommands
 */
class ShopSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * ShopSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if (!$this->checkPermission($sender, $name)) return;
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if($this->getPlugin()->getArenaManager()->getArenaByPlayer($sender) instanceof Arena) {
            $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender)->shopManager->openShop($sender, $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender)->getTeamByPlayer($sender));
        }
    }
}