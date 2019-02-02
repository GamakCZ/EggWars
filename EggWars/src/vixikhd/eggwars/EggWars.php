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
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\commands\EggWarsCommand;
use vixikhd\eggwars\commands\TeamCommand;
use vixikhd\eggwars\commands\VoteCommand;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;

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

    /** @var Arena[] $arenaManager */
    public $arenas = [];

    /** @var array $levels */
    public $levels = [];

    /** @var Command[] $commands */
    private $commands = [];

    /** @var array $configData */
    private $configData = [];

    public function onEnable() {
        self::$instance = $this;
        $this->initConfig();
        $this->registerCommands();
    }

    public function initConfig() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder()."levels")) {
            @mkdir($this->getDataFolder()."levels");
        }
        if(!is_dir($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder()."arenas");
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }
        $this->configData = $this->getConfig()->getAll();
    }

    private function registerCommands() {
        $this->commands["eggwars"] = new EggWarsCommand;
        $this->commands["vote"] = new VoteCommand;
        $this->commands["team"] = new TeamCommand;
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("eggwars", $command);
        }
    }

    /**
     * @return EggWars $plugin
     */
    public static function getInstance(): EggWars {
        return self::$instance;
    }
}
