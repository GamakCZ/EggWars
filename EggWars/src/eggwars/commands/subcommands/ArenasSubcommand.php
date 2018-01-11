<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\commands\EggWarsCommand;
use eggwars\EggWars;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class ListSubcommand
 * @package eggwars\commands\subcommands
 */
class ArenasSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * ArenasSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if($name != "arenas") return;
        if(!$this->checkPermission($sender, $name)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        $sender->sendMessage(EggWars::getPrefix()."§7Arenas: §9".$this->getPlugin()->getArenaManager()->getListArenasInString());
    }
}