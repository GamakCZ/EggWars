<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class ListSubcommand
 * @package eggwars\commands\subcommands
 */
class ArenasSubcommand extends SubCommand {

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if($this->checkPermission($sender, $name)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        $sender->sendMessage("§7Arenas: §9".$this->getPlugin()->getArenaManager()->getListArenasInString());
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew set <arena>");
        }
    }
}