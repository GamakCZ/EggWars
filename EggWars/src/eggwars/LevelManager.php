<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\level\EggWarsLevel;
use eggwars\utils\ConfigManager;
use pocketmine\level\Level;
use pocketmine\utils\Config;

/**
 * Class LevelManager
 * @package eggwars
 */
class LevelManager extends ConfigManager {

    /**
     * @var EggWarsLevel[] $levels
     */
    private $levels = [];

    public function __construct() {
        $this->loadLevels();
    }

    /**
     * @param int $teamsCount
     * @return array|bool
     */
    public function getLevelsForArena(int $teamsCount) {
        $levels = [];
        foreach ($this->levels as $level) {
            if($level->getTeamsCount() == $teamsCount) {
                array_push($levels, $level);
            }
        }
        shuffle($levels);
        if(count($levels) < 3) {
            if(count($levels) !== 0) {
                return [$levels[0], $levels[0], $levels[0]];
            }
            else {
                return false;
            }
        }
        return [$levels[0], $levels[1], $levels[2]];
    }

    public function loadLevels() {
        foreach (glob($this->getDataFolder()."levels/*.yml") as $file) {
            $this->levels[basename($file, ".yml")] = EggWarsLevel::loadFromConfig(new Config($file, Config::YAML));
        }
    }

    /**
     * @param $name
     * @return EggWarsLevel $name
     */
    public function getLevelByName($name): EggWarsLevel {
        return $this->levels[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function levelExists(string $name): bool {
        return isset($this->levels[$name]);
    }

    /**
     * @param Level $level
     * @param string $levelName
     * @param null $data
     */
    public function addLevel(Level $level, string $levelName, $data = null) {
        $data = is_array($data) ? $data : $this->defaultLevelData;
        $data["levelName"] = $level->getName();
        $data["name"] = $levelName;
        $this->levels[$levelName] = new EggWarsLevel($data);
    }
}