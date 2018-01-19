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
use eggwars\event\listener\LevelSetupManager;
use eggwars\position\EggWarsPosition;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class LevelSubcommand
 * @package eggwars\commands\subcommands
 */
class LevelSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * LevelSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$this->checkPermission($sender, $name)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        if(empty($args[0])) {
            $sender->sendMessage(EggWars::getPrefix()."§cUsage: §7/ew level <add|set|remove|list>");
            return;
        }
        switch ($args[0]) {
            case "add":
                if(!(count($args) > 2)) {
                    $sender->sendMessage(EggWars::getPrefix()."§cUsage: §7/ew level add <level> <customLevelName>");
                    break;
                }
                if(!$this->getPlugin()->getServer()->isLevelGenerated($args[1])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cLevel {$args[1]} does not found!");
                    break;
                }
                if($this->getPlugin()->getLevelManager()->levelExists($args[2])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cLevel {$args[2]} is already added!");
                    break;
                }
                $this->getPlugin()->getLevelManager()->addLevel($this->getPlugin()->getServer()->getLevelByName($args[1]), $args[2]);
                $sender->sendMessage(EggWars::getPrefix()."§aLevel {$args[2]} added!");
                break;
            case "set":
                if(empty($args[1])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cUsage: §7/ew level set <customLevelName>");
                    break;
                }
                if(!$this->getPlugin()->getLevelManager()->levelExists($args[1])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cLevel $args[1] does not found!");
                    break;
                }
                LevelSetupManager::addPlayer($sender, $this->getPlugin()->getLevelManager()->getLevelByName($args[1]));
                break;
            case "remove":
                if(empty($args[1])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cUsage: §7/ew remove <customLevelName>");
                    break;
                }
                if(!$this->getPlugin()->getLevelManager()->levelExists($args[1])) {
                    $sender->sendMessage(EggWars::getPrefix()."§cLevel $args[1] does not found!");
                    break;
                }
                $this->getPlugin()->getLevelManager()->removeLevel($args[1]);
                $sender->sendMessage(EggWars::getPrefix()."§aLevel removed!");
                break;
            case "list":
                $levels = $this->getPlugin()->getLevelManager()->getListLevelsInString();
                $sender->sendMessage(EggWars::getPrefix()."§aLevels: $levels §a.");
                break;
            default:
                $sender->sendMessage(EggWars::getPrefix()."§cUsage: §7/ew level <add|set|remove>");
                break;
        }
    }

}