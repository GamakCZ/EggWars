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

namespace vixikhd\eggwars\arena;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\scheduler\Task;
use pocketmine\tile\Sign;
use vixikhd\eggwars\utils\Time;

/**
 * Class ArenaScheduler
 * @package vixikhd\eggwars\arena
 */
class ArenaScheduler extends Task {

    /** @var Arena $plugin */
    public $plugin;

    /** @var int $startTime */
    public $startTime = 10;

    /** @var float|int $gameTime */
    public $gameTime = 20 * 60;

    /** @var int $restartTime */
    public $restartTime = 10;

    /** @var array $restartData */
    public $restartData = [];

    /**
     * ArenaScheduler constructor.
     * @param Arena $plugin
     */
    public function __construct(Arena $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick) {
        $this->reloadSign();

        if($this->plugin->setup) return;

        switch ($this->plugin->phase) {
            case Arena::PHASE_LOBBY:
                if(count($this->plugin->players) >= 1/*2*/) {
                    $this->plugin->broadcastMessage("§a> Starting in " . Time::calculateTime($this->startTime) . " sec.", Arena::MSG_TIP);
                    $this->startTime--;
                    if($this->startTime == 0) {
                        $this->plugin->startGame();
                        foreach ($this->plugin->players as $player) {
                            $this->plugin->levelManager->getLevel()->addSound(new AnvilUseSound($player->asVector3()));
                        }
                    }
                    else {
                        foreach ($this->plugin->players as $player) {
                            $this->plugin->levelManager->getLevel()->addSound(new ClickSound($player->asVector3()));
                        }
                    }
                }
                else {
                    $this->plugin->broadcastMessage("§c> You need more players to start a game!", Arena::MSG_TIP);
                    $this->startTime = 10;
                }
                break;
            case Arena::PHASE_GAME:
                $this->plugin->broadcastMessage($this->getScoreText(), Arena::MSG_POPUP);
                $this->spawnIngots();
                $this->gameTime--;
                break;
            case Arena::PHASE_RESTART:
                $this->plugin->broadcastMessage("§a> Restarting in {$this->restartTime} sec.", Arena::MSG_TIP);
                $this->restartTime--;

                switch ($this->restartTime) {
                    case 0:

                        foreach ($this->plugin->players as $player) {
                            $player->teleport($this->plugin->plugin->getServer()->getDefaultLevel()->getSpawnLocation());

                            $player->getInventory()->clearAll();
                            $player->getArmorInventory()->clearAll();
                            $player->getCursorInventory()->clearAll();

                            $player->setFood(20);
                            $player->setHealth(20);

                            $player->setGamemode($this->plugin->plugin->getServer()->getDefaultGamemode());
                        }
                        $this->plugin->loadArena();
                        $this->reloadTimer();
                        break;
                }
                break;
        }
    }

    public function spawnIngots() {

    }

    public function getScoreText(): string {
        $text = "§a> Time to end: " . (string)Time::calculateTime($this->gameTime) . "\n";
        foreach ($this->plugin->data["teams"] as $team => $color) {
            if($this->plugin->aliveTeams[$team]) {
                $text .= $color . $team . " §7[§aX§7]§a ";
            }
            else {
                $text .= $color . $team . " §7[§4X§7]§a ";
            }
        }
        return $text;
    }

    public function reloadSign() {
        if(!is_array($this->plugin->data["joinsign"]) || empty($this->plugin->data["joinsign"])) return;

        $signPos = new Position($this->plugin->data["joinsign"][0], $this->plugin->data["joinsign"][1], $this->plugin->data["joinsign"][2], $this->plugin->plugin->getServer()->getLevelByName($this->plugin->data["joinsign"][3]));

        if(!$signPos->getLevel() instanceof Level) return;

        $signText = [
            "§3§lEggWars",
            "§9[ §b? / ? §9]",
            "§6Setup",
            "§6Wait few sec..."
        ];

        if($signPos->getLevel()->getTile($signPos) === null) return;

        if($this->plugin->setup) {
            /** @var Sign $sign */
            $sign = $signPos->getLevel()->getTile($signPos);
            $sign->setText($signText[0], $signText[1], $signText[2], $signText[3]);
            return;
        }

        $signText[1] = "§9[ §b" . count($this->plugin->players) . " / " . (string)($this->plugin->data["playersperteam"] * count($this->plugin->data["teams"])) . " §9]";

        switch ($this->plugin->phase) {
            case Arena::PHASE_LOBBY:
                if(count($this->plugin->players) >= (string)($this->plugin->data["playersperteam"] * count($this->plugin->data["teams"]))) {
                    $signText[2] = "§6Full";
                    $signText[3] = "§8Map: §7{$this->plugin->levelManager->getLevel()->getFolderName()}";
                }
                else {
                    $signText[2] = "§aJoin";
                    $signText[3] = "§8Map: §7{$this->plugin->levelManager->getLevel()->getFolderName()}";
                }
                break;
            case Arena::PHASE_GAME:
                $signText[2] = "§5InGame";
                $signText[3] = "§8Map: §7{$this->plugin->levelManager->getLevel()->getFolderName()}";
                break;
            case Arena::PHASE_RESTART:
                $signText[2] = "§cRestarting...";
                $signText[3] = "§8Map: §7{$this->plugin->levelManager->getLevel()->getFolderName()}";
                break;
        }

        /** @var Sign $sign */
        $sign = $signPos->getLevel()->getTile($signPos);
        if($sign instanceof Sign) // Chest->setText() doesn't work :D
            $sign->setText($signText[0], $signText[1], $signText[2], $signText[3]);
    }

    public function reloadTimer() {
        $this->startTime = 10;
        $this->gameTime = 20 * 60;
        $this->restartTime = 10;
    }
}