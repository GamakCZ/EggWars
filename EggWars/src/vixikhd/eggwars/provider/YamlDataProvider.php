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

namespace vixikhd\eggwars\provider;

use pocketmine\level\Level;
use pocketmine\utils\Config;
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;

/**
 * Class YamlDataProvider
 * @package skywars\provider
 */
class YamlDataProvider {

    /** @var EggWars $plugin */
    private $plugin;

    /**
     * YamlDataProvider constructor.
     * @param EggWars $plugin
     */
    public function __construct(EggWars $plugin) {
        $this->plugin = $plugin;
        $this->init();
        $this->loadLevels();
        $this->loadArenas();
    }

    public function saveAll() {
        $this->saveLevels();
        $this->saveArenas();
    }

    public function init() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder() . "arenas")) {
            @mkdir($this->getDataFolder() . "arenas");
        }
        if(!is_dir($this->getDataFolder() . "levels")) {
            @mkdir($this->getDataFolder() . "levels");
        }
        if(!is_dir($this->getDataFolder() . "saves")) {
            @mkdir($this->getDataFolder() . "saves");
        }
    }

    public function loadLevels() {
        foreach (glob($this->getDataFolder() . "levels" . DIRECTORY_SEPARATOR . "*.yml") as $levelFile) {
            $config = new Config($levelFile, Config::YAML);
            $this->plugin->levels[basename($levelFile, ".yml")] = $config->getAll();
        }
    }

    public function saveLevels() {
        foreach ($this->plugin->levels as $fileName => $data) {
            $config = new Config($this->getDataFolder() . "levels" . DIRECTORY_SEPARATOR . $fileName . ".yml", Config::YAML);
            $config->setAll($data);
            $config->save();
        }
    }

    public function loadArenas() {
        foreach (glob($this->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR . "*.yml") as $arenaFile) {
            $config = new Config($arenaFile, Config::YAML);
            $this->plugin->arenas[basename($arenaFile, ".yml")] = new Arena($this->plugin, $config->getAll(\false));
        }
    }

    public function saveArenas() {
        foreach ($this->plugin->arenas as $fileName => $arena) {
            // TODO: implement saving when arena is ingame
            $config = new Config($this->getDataFolder() . "arenas" . DIRECTORY_SEPARATOR . $fileName . ".yml", Config::YAML);
            $config->setAll($arena->data);
            $config->save();
        }
    }

    /**
     * @return string $dataFolder
     */
    private function getDataFolder(): string {
        return $this->plugin->getDataFolder();
    }
}
