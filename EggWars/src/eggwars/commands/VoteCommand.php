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

namespace eggwars\commands;

use eggwars\arena\Arena;
use eggwars\EggWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class VoteCommand
 * @package eggwars\commands
 */
class VoteCommand extends Command implements PluginIdentifiableCommand {

    /**
     * VoteCommand constructor.
     */
    public function __construct() {
        parent::__construct("vote", "Vote for level", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage(EggWars::getPrefix()."§cThis command can be used only in-game!");
            return;
        }
        if(!$this->getPlugin()->getArenaManager()->getArenaByPlayer($sender) instanceof Arena) {
            $sender->sendMessage(EggWars::getPrefix()."§cThis command can be used only in-arena!");
            return;
        }
        $arena = $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender);
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/vote <map: 1-3>");
            return;
        }
        $arena->voteManager->addVote($sender, $args[0]);
    }

    /**
     * @return Plugin|EggWars
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}