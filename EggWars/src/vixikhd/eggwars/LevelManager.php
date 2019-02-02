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

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\level\EggWarsLevel;
use vixikhd\eggwars\utils\ConfigManager;
use pocketmine\level\Level;
use pocketmine\utils\Config;

/**
 * Class LevelManager
 * @package eggwars
 */
class LevelManager extends ConfigManager {

    /** @var EggWarsLevel[] $level */
    private $levels = [];

    /**
     * LevelManager constructor.
     */
    public function __construct() {
        $this->loadLevels();
    }


    /**
     * @param Arena $arena
     * @return array|bool $levels
     */
    public function getLevelsForArena(Arena $arena) {
        $levels = [];
        foreach($this->levels as $level) {
            if(in_array($arena->getName(), $level->data["arenas"])) {
                if($level->isValid()) {
                    array_push($levels, $level);
                }
                else {
                    EggWars::getInstance()->getLogger()->critical("§cLevel {$level->getCustomName()} is not valid!");
                }
            }
        }
        check:
        if(count($levels) < 3) {
            if(count($levels) === 0) {
                return false;
            }
            array_push($levels, $levels[0]);
            goto check;
        }
        shuffle($levels);
        return $levels;
    }


    public function saveLevels() {
        /**
         * @var string $name
         * @var EggWarsLevel $level
         */
        foreach ($this->levels as $name => $level) {
            if(is_file($this->getDataFolder()."levels/".$name.".yml")) {
                $config = new Config($this->getDataFolder()."levels/".$name.".yml", Config::YAML);
                $config->setAll($level->data);
                $config->save();
                #$level->getLevel()->unload();
            }
            else {
                $config = new Config($this->getDataFolder()."levels/".$name.".yml", Config::YAML, $level->data);
                $config->save();
            }
            EggWars::getInstance()->getLogger()->notice("Level {$name} is successfully saved!");
        }
    }

    public function loadLevels() {
        foreach (glob($this->getDataFolder()."levels/*.yml") as $file) {
            $config = new Config($file, Config::YAML);
            $this->levels[basename($file, ".yml")] = new EggWarsLevel($config->getAll());
        }
    }

    /**
     * @param $name
     * @return EggWarsLevel $name
     */
    public function getLevelByName($name) {
        return isset($this->levels[$name]) ? $this->levels[$name] : null;
    }

    /**
     * @return string $list
     */
    public function getListLevelsInString(): string {
        $list = [];
        foreach ($this->levels as $name => $level) {
            array_push($list, "§b$name");
        }
        if(count($list) == 0) {
            return "§cThere are no levels";
        }
        return "§aLevels (".count($list)."): ".implode("\n", $list);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function levelExists(string $name): bool {
        return isset($this->levels[$name]);
    }

    /**
     * @param string $levelName
     */
    public function removeLevel(string $levelName) {
        unset($this->levels[$levelName]);
        unlink($this->getDataFolder()."levels/$levelName.yml");
    }

    /**
     * @param Level $level
     * @param string $levelName
     * @param null $data
     */
    public function addLevel(Level $level, string $levelName, $data = null) {
        $data = is_array($data) ? $data : $this->defaultLevelData;
        $data["levelName"] = $level->getName();
        $data["folderName"] = $level->getFolderName();
        $data["name"] = $levelName;
        $this->levels[$levelName] = new EggWarsLevel($data);
    }
}