<?php

namespace EggWars\Arena;

use EggWars\EggWars;
use pocketmine\level\Level;
use pocketmine\utils\Config;

class Arena {

    /** @var EggWars */
    public $plugin;

    /** @var  SetupManager */
    public $setupManager;

    public $arenas = [];

    /** @var Level[] */
    public $runningLevels = [];

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function reloadArenas() {

    }

    /**
     * @param $name
     * @return bool
     */
    public function mapExists($name) {
        if(is_file($this->getWorldPath()."{$name}.yml")) {
            return true;
        }
    }

    /**
     * @param $arena
     * @return bool
     */
    public function arenaExists($arena) {
        if(is_file($this->getArenaPath()."{$arena}.yml")) {
            return true;
        }
    }

    /**
     * @return array
     */
    public function getArenas() {
        return $this->arenas;
    }

    /**
     * @param $arena
     * @return Config
     */
    public function getConfig($arena) {
        return new Config($this->plugin->getDataFolder()."arenas/{$arena}.yml", Config::YAML);
    }

    /**
     * @return string
     */
    public function getWorldPath() {
        return $this->plugin->getServer()->getDataPath()."worlds/";
    }

    /**
     * @return string
     */
    public function getArenaPath() {
        return $this->plugin->getDataFolder()."arenas/";
    }
}
