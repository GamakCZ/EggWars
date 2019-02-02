<?php

/**
 * Copyright 2018 GamakCZ
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace vixikhd\eggwars\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;

/**
 * Class EggWarsCommand
 * @package vixikhd\eggwars\commands
 */
class EggWarsCommand extends Command implements PluginIdentifiableCommand {

    /** @var EggWars $plugin */
    protected $plugin;

    /**
     * EggWarsCommand constructor.
     * @param EggWars $plugin
     */
    public function __construct(EggWars $plugin) {
        $this->plugin = $plugin;
        $this->setPermission("ew.cmd");
        parent::__construct("eggwars", "EggWars commands", \null, ["ew"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!isset($args[0])) {
            $sender->sendMessage("§cUsage: §7/ew help");
            return;
        }

        if(!$sender->hasPermission("ew.cmd") || !$sender instanceof Player) return;

        switch ($args[0]) {
            case "help":
                if(!$sender->hasPermission("ew.cmd.help")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                $sender->sendMessage("§a> EggWars commands:\n" .
                    "§7/ew help : Displays list of EggWars commands\n".
                    "§7/ew create : Create EggWars arena\n".
                    "§7/ew remove : Remove EggWars arena\n".
                    "§7/ew set : Set EggWars arena\n".
                    "§7/ew arenas : Displays list of arenas\n".
                    "§7/ew level : Level settings");

                break;
            case "create":
                if(!$sender->hasPermission("ew.cmd.create")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                if(!isset($args[1])) {
                    $sender->sendMessage("§cUsage: §7/ew create <arenaName>");
                    break;
                }
                if(isset($this->plugin->arenas[$args[1]])) {
                    $sender->sendMessage("§c> Arena $args[1] already exists!");
                    break;
                }
                $this->plugin->arenas[$args[1]] = new Arena($this->plugin, []);
                $sender->sendMessage("§a> Arena $args[1] created!");
                break;
            case "remove":
                if(!$sender->hasPermission("ew.cmd.remove")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                if(!isset($args[1])) {
                    $sender->sendMessage("§cUsage: §7/ew remove <arenaName>");
                    break;
                }
                if(!isset($this->plugin->arenas[$args[1]])) {
                    $sender->sendMessage("§c> Arena $args[1] was not found!");
                    break;
                }

                /** @var Arena $arena */
                $arena = $this->plugin->arenas[$args[1]];

                // TODO
                /*foreach ($arena->players as $player) {
                    $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSpawnLocation());
                }*/

                if(is_file($file = $this->plugin->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR . $args[1] . ".yml")) unlink($file);
                unset($this->plugin->arenas[$args[1]]);

                $sender->sendMessage("§a> Arena removed!");
                break;
            case "set":
                if(!$sender->hasPermission("ew.cmd.set")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                if(!$sender instanceof Player) {
                    $sender->sendMessage("§c> This command can be used only in-game!");
                    break;
                }
                if(!isset($args[1])) {
                    $sender->sendMessage("§cUsage: §7/ew set <arenaName>");
                    break;
                }
                if(isset($this->plugin->setters[$sender->getName()])) {
                    $sender->sendMessage("§c> You are already in setup mode!");
                    break;
                }
                if(!isset($this->plugin->arenas[$args[1]])) {
                    $sender->sendMessage("§c> Arena $args[1] does not found!");
                    break;
                }
                $sender->sendMessage("§a> You are joined arena setup mode.\n".
                    "§7- use §lhelp §r§7to display available commands\n"  .
                    "§7- or §ldone §r§7to leave setup mode");
                $this->plugin->setters[$sender->getName()] = $this->plugin->arenas[$args[1]];
                break;
            case "level":
                if(!$sender->hasPermission("ew.cmd.level")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                if(!isset($args[1])) {
                    $sender->sendMessage("§cUsage: §7/ew level <add|rm|set|list>");
                    break;
                }
                switch ($args[1]) {
                    case "add":
                        if(!isset($args[2])) {
                            $sender->sendMessage("§cUsage: §7/ew level add <level>");
                            break;
                        }
                        if(!$this->plugin->getServer()->isLevelGenerated($args[2])) {
                            $sender->sendMessage("§c> Level {$args[2]} not found!");
                            break;
                        }
                        if(isset($this->plugin->levels[$args[2]])) {
                            $sender->sendMessage("§c> Level {$args[2]} is already added!");
                            break;
                        }
                        if(!$this->plugin->getServer()->isLevelLoaded($args[2])) {
                            $sender->sendMessage("§6> Loading level {$args[2]}...");
                            $this->plugin->getServer()->loadLevel($args[2]);
                        }
                        $level = $this->plugin->getServer()->getLevelByName($args[2]);
                        $this->plugin->levels[$args[2]] = [
                            "enabled" => false,
                            "name" => "", // custom (displayed) name
                            "level" => $level->getFolderName(), // folder name of level
                            "arena" => "", // arena
                            "teams" => []
                        ];
                        $sender->sendMessage("§a> Level {$args[2]} added! Setup it using §7/ew level set <level>§a!");
                        break;
                    case "rm":
                        if(!isset($args[2])) {
                            $sender->sendMessage("§cUsage: §7/ew level add <level>");
                            break;
                        }
                        if(!isset($this->plugin->levels[$args[2]])) {
                            $sender->sendMessage("§c> Level {$args[2]} not found!");
                            break;
                        }
                        unset($this->plugin->levels[$args[2]]);
                        if(is_file($this->plugin->getDataFolder() . "levels/{$args[2]}.yml")) {
                            unlink($this->plugin->getDataFolder() . "levels/{$args[2]}.yml");
                        }
                        $sender->sendMessage("§a> Level {$args[2]} removed!");
                        break;
                    case "set":
                        if(!isset($args[2])) {
                            $sender->sendMessage("§cUsage: §7/ew level add <level>");
                            break;
                        }
                        if(!isset($this->plugin->levels[$args[2]])) {
                            $sender->sendMessage("§c> Level {$args[2]} not found!");
                            break;
                        }
                        $this->plugin->setters[$sender->getName()] = $args[2];
                        $sender->sendMessage("§a> You are joined level setup mode.\n".
                            "§7- use §lhelp §r§7to display available commands\n"  .
                            "§7- or §ldone §r§7to leave setup mode");
                        break;
                    case "list":
                        $list = "§7> Levels:\n";
                        foreach ($this->plugin->levels as $name => ["enabled" => $enabled, "name" => $customName]) {
                            if($enabled) {
                                $list .= "§7- $name ($customName): §cdisabled\n";
                            }
                            else {
                                $list .= "§7- $name ($customName): §aenabled\n";
                            }
                        }
                        $sender->sendMessage($list);
                        break;
                    default:
                        $sender->sendMessage("§cUsage: §7/ew level <add|rm|set|list>");
                        break;
                }
                break;
            case "arenas":
                if(!$sender->hasPermission("ew.cmd.arenas")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                if(count($this->plugin->arenas) === 0) {
                    $sender->sendMessage("§6> There are 0 arenas.");
                    break;
                }
                $list = "§7> Arenas:\n";
                foreach ($this->plugin->arenas as $name => $arena) {
                    if($arena->setup) {
                        $list .= "§7- $name : §cdisabled\n";
                    }
                    else {
                        $list .= "§7- $name : §aenabled\n";
                    }
                }
                $sender->sendMessage($list);
                break;
            default:
                if(!$sender->hasPermission("ew.cmd.help")) {
                    $sender->sendMessage("§cYou have not permissions to use this command!");
                    break;
                }
                $sender->sendMessage("§cUsage: §7/ew help");
                break;
        }

    }

    /**
     * @return EggWars|Plugin $plugin
     */
    public function getPlugin(): Plugin {
        return $this->plugin;
    }

}
