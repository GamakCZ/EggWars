<?php

/**
 *    Copyright 2018-2019 GamakCZ
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace vixikhd\eggwars;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\commands\EggWarsCommand;
use vixikhd\eggwars\commands\TeamCommand;
use vixikhd\eggwars\commands\VoteCommand;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use vixikhd\eggwars\provider\YamlDataProvider;
use vixikhd\eggwars\utils\Color;
use vixikhd\eggwars\utils\Math;

/**
 * Class EggWars
 * @package vixikhd\eggwars
 *
 * @author VixikCZ
 * @version 1.0.0-beta1
 * @api 3.0.0
 */
class EggWars extends PluginBase implements Listener, DefaultArenaData, DefaultLevelData {

    /** @var EggWars $instance */
    private static $instance;

    /** @var Arena[]|array $arenaManager */
    public $arenas = [];

    /** @var array $levels */
    public $levels = [];

    /** @var YamlDataProvider $dataProvider */
    public $dataProvider;

    /** @var Command[] $commands */
    private $commands = [];

    /** @var Arena[] $setters */
    public $setters = [];

    public function onEnable() {
        self::$instance = $this;
        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    private function registerCommands() {
        $this->commands["EggWars"] = new EggWarsCommand($this);
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("EggWars", $command);
        }
    }

    public function onMessage(PlayerChatEvent $event) {
        $player = $event->getPlayer();

        if(!isset($this->setters[$player->getName()])) {
            return;
        }

        $event->setCancelled(\true);
        $args = explode(" ", $event->getMessage());

        if($this->setters[$player->getName()] instanceof Arena) {
            $arena = $this->setters[$player->getName()];
            switch ($args[0]) {
                case "help":
                    $player->sendMessage("§a> EggWars arena setup help (1/1):\n".
                        "§7help : Displays list of available setup commands\n" .
                        "§7ppt : Update players per team count\n".
                        "§7lobby : Update lobby position\n".
                        "§7addteam : Create new tean\n".
                        "§7rmteam : Remove team\n".
                        "§7enable : Enable the arena");
                    break;
                case "ppt":
                    if(!isset($args[1]) || !is_numeric($args[1])) {
                        $player->sendMessage("§cUsage: §7ppt <playersPerTeam>");
                        break;
                    }
                    $arena->data["playersperteam"] = (int)$args[1];
                    $player->sendMessage("§a> Players per team count updated to {$args[1]}!");
                    break;
                case "lobby":
                    $pos = Math::ceilPosition($player);
                    $arena->data["lobby"] = [$pos->getX(), $pos->getY(), $pos->getZ(), $pos->getLevel()->getFolderName()];
                    $player->sendMessage("§a> Lobby position updated to {$pos->getX()}, {$pos->getY()}, {$pos->getZ()}!");
                    break;
                case "addteam":
                    if(!isset($args[1]) || !isset($args[2])) {
                        $player->sendMessage("§cUsage: §7addteam <team> <color>");
                        break;
                    }
                    if(isset($arena->data["teams"][$args[1]])) {
                        $player->sendMessage("§c> Team {$args[1]} already exists!");
                        break;
                    }
                    $args[2] = str_replace("&", "§", $args[2]);
                    if(!Color::mcColorExists($args[2])) {
                        $player->sendMessage("§c> Color {$args[2]} not found! Color examples: &e -> yellow, &b -> aqua, ...");
                        break;
                    }
                    $arena->data["teams"][$args[1]] = $args[2];
                    $player->sendMessage("§a> Team {$args[2]}{$args[1]} §acreated!");
                    break;
                case "rmteam":
                    if(!isset($args[1])) {
                        $player->sendMessage("§cUsage: §7rmteam <team>");
                        break;
                    }
                    if(!isset($arena->data["teams"][$args[1]])) {
                        $player->sendMessage("§c> Team {$args[1]} not found!");
                        break;
                    }
                    unset($arena->data["teams"][$args[1]]);
                    $player->sendMessage("§a> Team {$args[1]} removed!");
                    break;

            }
            return;
        }
    }

    /**
     * @return EggWars $plugin
     */
    public static function getInstance(): EggWars {
        return self::$instance;
    }
}
