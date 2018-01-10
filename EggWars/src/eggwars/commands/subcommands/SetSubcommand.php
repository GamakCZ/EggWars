<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\EggWars;
use eggwars\event\listener\ArenaSetupManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class SetSubcommand
 * @package eggwars\commands\subcommands
 */
class SetSubcommand extends SubCommand {

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