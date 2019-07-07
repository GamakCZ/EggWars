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

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\arena\IngotGeneratorData;
use vixikhd\eggwars\commands\EggWarsCommand;
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
class EggWars extends PluginBase implements Listener, IngotGeneratorData {

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

    /** @var array $setupData */
    public $setupData = [];

    public function onEnable() {
        self::$instance = $this;
        $this->dataProvider = new YamlDataProvider($this);
        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDisable() {
        $this->dataProvider->saveAll();
    }

    private function registerCommands() {
        $this->commands["EggWars"] = new EggWarsCommand($this);
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("EggWars", $command);
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onMessage(PlayerChatEvent $event) {
        $player = $event->getPlayer();

        if(!isset($this->setters[$player->getName()])) {
            return;
        }

        $event->setCancelled(true);
        $args = explode(" ", $event->getMessage());

        if($args[0] == "done") {
            $player->sendMessage("§a> You have successfully left setup mode!");
            unset($this->setters[$player->getName()]);
            return;
        }

        if($this->setters[$player->getName()] instanceof Arena) {
            $arena = $this->setters[$player->getName()];
            switch ($args[0]) {
                case "help":
                    $player->sendMessage("§a> EggWars arena setup help (1/1):\n".
                        "§7help : Displays list of available setup commands\n" .
                        "§7ppt : Update players per team count\n".
                        "§7tts : Update count teams required to start\n" .
                        "§7joinsign : Set join sign\n" .
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
                case "tts":
                    if(!isset($args[1]) || !is_numeric($args[1])) {
                        $player->sendMessage("§cUsage: §7ppt <playersPerTeam>");
                        break;
                    }
                    $arena->data["teamstostart"] = (int)$args[1];
                    $player->sendMessage("§a> Players per team count updated to {$args[1]}!");
                    break;
                case "joinsign":
                    $this->setupData[$player->getName()] = [0];
                    $player->sendMessage("§a> Break the block to set the sign!");
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
                case "enable":
                    if(!$arena->enable(true)) {
                        $player->sendMessage("§c> Could not enable arena, complete setup first.");
                        break;
                    }
                    $player->sendMessage("§a> Arena enabled!");
                    break;
                default:
                    $player->sendMessage("§6> You are in setup mode.\n".
                        "§7- use §lhelp §r§7to display available commands\n".
                        "§7- or §ldone §r§7to leave setup mode");
                    break;

            }
            return;
        }

        if(!is_string($this->setters[$player->getName()])) return;

        /** @var string $levelData */
        $levelData = $this->setters[$player->getName()]; // level's name

        switch ($args[0]) {
            case "help":
                $player->sendMessage("§a> EggWars level setup help (1/1):\n".
                    "§7help : Displays list of available setup commands\n" .
                    "§7setarena : Set arena to import teams\n".
                    "§7name : Set custom level name (required)\n".
                    "§7spawn : Update team spawn\n" .
                    "§7egg : Update team egg\n" .
                    "§7checksigns : Loads all the signs in the world\n" .
                    "§7enable : Enables the level");
                break;
            case "setarena":
                if(!isset($args[1])) {
                    $player->sendMessage("§cUsage: §7setarena <arena>");
                    break;
                }
                if(!isset($this->arenas[$args[1]])) {
                    $player->sendMessage("§c> Arena {$args[1]} not found!");
                    break;
                }
                $arena = $this->arenas[$args[1]];
                foreach ($arena->data["teams"] as $team => $color) {
                    $player->sendMessage("§a> Importing {$color}{$team} §ateam...");
                    $this->levels[$levelData]["teams"][$team] = [];
                }
                $this->levels[$levelData]["arena"] = $args[1];
                $arena->data["levels"][] = $levelData;
                $player->sendMessage("§a> Arena {$args[1]} imported!");
                break;
            case "name":
                if(!isset($args[1])) {
                    $player->sendMessage("§cUsage: §7name <customLevelName>");
                    break;
                }
                $this->levels[$levelData]["name"] = $args[1];
                $player->sendMessage("§a> Name updated to {$args[1]}!");
                break;
            case "spawn":
                if(!isset($args[1])) {
                    $player->sendMessage("§cUsage: §7spawn <team>");
                    break;
                }
                if($player->getLevel()->getFolderName() != $levelData) {
                    $player->sendMessage("§c> Spawn must been set in arena level!");
                    break;
                }
                if($this->levels[$levelData]["arena"] == "") {
                    $player->sendMessage("§c> Import teams first!");
                    break;
                }
                if(!isset($this->arenas[$this->levels[$levelData]["arena"]])) {
                    $player->sendMessage("§c> Arena is not implemented! Import teams again!\n§6> Imported teams were removed.");
                    $this->levels[$levelData]["arena"] = "";
                    $this->levels[$levelData]["teams"] = [];
                    break;
                }
                if(!isset($this->levels[$levelData]["teams"][$args[1]])) {
                    $player->sendMessage("§c> Team {$args[1]} isn't imported!");
                    break;
                }
                $pos = $player->ceil();
                $this->levels[$levelData]["teams"][$args[1]]["spawn"] = [$pos->getX(), $pos->getY(), $pos->getZ()];
                $player->sendMessage("§a> Spawn position updated to {$pos->getX()}, {$pos->getY()}, {$pos->getZ()}!");
                break;
            case "egg":
                if(!isset($args[1])) {
                    $player->sendMessage("§cUsage: §7egg <team>");
                    break;
                }
                if($this->levels[$levelData]["arena"] == "") {
                    $player->sendMessage("§c> Import teams first!");
                    break;
                }
                if(!isset($this->arenas[$this->levels[$levelData]["arena"]])) {
                    $player->sendMessage("§c> Arena is not implemented! Import teams again!\n§6> Imported teams were removed.");
                    $this->levels[$levelData]["arena"] = "";
                    $this->levels[$levelData]["teams"] = [];
                    break;
                }
                if(!isset($this->levels[$levelData]["teams"][$args[1]])) {
                    $player->sendMessage("§c> Team {$args[1]} isn't imported!");
                    break;
                }
                $this->setupData[$player->getName()] = [1, $args[1]];
                $player->sendMessage("§a> Break the block to set the egg!");
                break;
            case "checksigns":
                if(!$this->getServer()->isLevelGenerated($levelData)) {
                    $player->sendMessage("§c> Level not found.");
                    break;
                }
                if(!$this->getServer()->isLevelLoaded($levelData)) {
                    $this->getServer()->loadLevel($levelData);
                }
                foreach ($this->getServer()->getLevelByName($levelData)->getTiles() as $sign) {
                    if($sign instanceof Sign) {
                        $text = $sign->getText();
                        if(strtolower($text[0]) == "generator") {
                            $level = -1;
                            $ingot = "";
                            $maxLevel = false;

                            switch (ucfirst($text[1])) {
                                case "Iron":
                                    $ingot = "§l§7Iron";
                                    if(($l = (int)trim($text[2])) <= self::INGOT_IRON["maxLevel"]) {
                                        $level = $l;
                                        if($level == self::INGOT_IRON["maxLevel"]) {
                                            $maxLevel = true;
                                        }
                                    }
                                    else {
                                        $level = 1;
                                    }
                                    break;
                                case "Gold":
                                    $ingot = "§l§6Gold";
                                    if(($l = (int)trim($text[2])) <= self::INGOT_GOLD["maxLevel"]) {
                                        $level = $l;
                                        if($level == self::INGOT_GOLD["maxLevel"]) {
                                            $maxLevel = true;
                                        }
                                    }
                                    else {
                                        $level = 1;
                                    }
                                    break;
                                case "Diamond":
                                    $ingot = "§l§bDiamond";
                                    if(($l = (int)trim($text[2])) <= self::INGOT_DIAMOND["maxLevel"]) {
                                        $level = $l;
                                        if($level == self::INGOT_DIAMOND["maxLevel"]) {
                                            $maxLevel = true;
                                        }
                                    }
                                    else {
                                        $level = 1;
                                    }
                                    break;
                            }

                            if($level !== -1 && $ingot !== "") {
                                $sign->setText("Generator", $ingot, "§fLevel: {$level}", ($maxLevel ? "§a§oMax level" : "§e§oClick to upgrade."));
                            }
                        }
                    }
                }
                break;
            case "enable":
                $data = $this->levels[$levelData];
                if(!isset($data["name"]) || $data["name"] == "") {
                    $data["name"] = $data["level"];
                }
                if($data["arena"] == "" || !isset($this->arenas[$data["arena"]])) {
                    $player->sendMessage("§c> Arena of level $levelData wasn't found.");
                    break;
                }
                $arena = $this->arenas[$data["arena"]];
                $problem = false;
                foreach ($data["teams"] as $team => $teamInformation) {
                    if(count($teamInformation) < 2) {
                        $problem = true;
                    }
                    foreach ($teamInformation as $posName => $vector) {
                        if(count($vector) < 3) {
                            $problem = true;
                        }
                    }
                }

                if(count($arena->data["teams"]) != count($data["teams"]) || $problem) {
                    $player->sendMessage("§c> Check that you have set all the teams.");
                    return;
                }
                $this->levels[$levelData]["enabled"] = true;

                if(!$this->getServer()->isLevelGenerated($levelData)) {
                    $player->sendMessage("§c> Level not found!");
                    break;
                }

                if(!$this->getServer()->isLevelLoaded($levelData)) {
                    $this->getServer()->loadLevel($levelData);
                }

                $level = $this->getServer()->getLevelByName($levelData);
                $arena->mapReset->saveMap($level);
                $player->sendMessage("§6> Level saved.");
                $player->sendMessage("§a> Level is successfully enabled!");
                break;
            default:
                $player->sendMessage("§6> You are in setup mode.\n".
                    "§7- use §lhelp §r§7to display available commands\n".
                    "§7- or §ldone §r§7to leave setup mode");
                break;
        }
    }

    /**
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        if($event->getBlock()->getId() == Block::DRAGON_EGG) {
            $event->getBlock()->getLevel()->setBlock($event->getBlock(), Block::get(Block::DRAGON_EGG));
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(isset($this->setupData[$player->getName()])) {
            switch ($this->setupData[$player->getName()][0]) {
                case 0: // sign
                    if(!isset($this->setters[$player->getName()])) {
                        unset($this->setupData[$player->getName()]);
                        return;
                    }
                    $event->setCancelled(true);
                    $arena = $this->setters[$player->getName()];
                    $pos = Math::ceilPosition($event->getBlock());
                    $arena->data["joinsign"] = [$pos->getX(), $pos->getY(), $pos->getZ(), $pos->getLevel()->getFolderName()];
                    $player->sendMessage("§a> Join sign updated to {$pos->getX()}, {$pos->getY()}, {$pos->getZ()}");
                    break;
                case 1: // egg
                    if(!isset($this->setters[$player->getName()])) {
                        unset($this->setupData[$player->getName()]);
                        return;
                    }
                    $event->setCancelled(true);
                    if($event->getBlock()->getLevel()->getFolderName() != $this->setters[$player->getName()]) {
                        $player->sendMessage("§a> Egg must been set in arena level!");
                        break;
                    }
                    $pos = $event->getBlock()->ceil();
                    $player->sendMessage("§a> Egg updated to {$pos->getX()}, {$pos->getY()}, {$pos->getZ()}");
                    /** @var string $index */
                    $index = $this->setters[$player->getName()];
                    $this->levels[$index]["teams"][$this->setupData[$player->getName()][1]]["egg"] = [$pos->getX(), $pos->getY(), $pos->getZ()];
                    break;
            }

            unset($this->setupData[$player->getName()]);
        }
        $block = $event->getBlock();
    }

    /**
     * @return EggWars $plugin
     */
    public static function getInstance(): EggWars {
        return self::$instance;
    }
}
