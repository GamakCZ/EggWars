<?php

declare(strict_types=1);

namespace eggwars\utils;

use eggwars\EggWars;
use pocketmine\Server;
use pocketmine\utils\Config;

/**
 * Class ConfigManager
 * @package eggwars\utils
 */
class ConfigManager {

    /**
     * @var array $mainConfigData
     */
    private $mainConfigData = [];

    /**
     * @var array $defaultArenaData
     */
    public $defaultArenaData = [
        "enabled" => false,
        "startTime" => 30,
        "gameTime" => 600,
        "restartTime" => 20,
        "teamsToStart" => 2,
        "playersPerTeam" => 2,
        "teams" => [
            "red" => [
                "color" => "§4",
                "egg" => [100, 4, 100],
                "spawn" => [100, 5, 100],
            ],
            "blue" => [
                "color" => "§9",
                "egg" => [80, 4, 80],
                "spawn" => [80, 5, 80]
            ]
        ]
    ];

    /**
     * @param mixed $k
     * @param mixed $v
     */
    public function setToConfig($k, $v) {
        $this->mainConfigData[$k] = $v;
    }

    /**
     * @param mixed $k
     * @return mixed
     */
    public function getFromConfig($k):mixed {
        return $this->mainConfigData[$k];
    }

    /**
     * @return string $dataFolder
     */
    public function getDataFolder():string {
        return EggWars::getInstance()->getDataFolder();
    }

    /**
     * @return string $arenaDataFolder
     */
    public function getArenaDataFolder():string {
        return EggWars::getInstance()->getDataFolder()."arenas/";
    }

    /**
     * @return Config $config
     */
    private function getConfig():Config {
        return EggWars::getInstance()->getConfig();
    }


    /**
     * @return string $dataPath
     */
    public function getDataPath():string {
        return Server::getInstance()->getDataPath();
    }

    public function initConfig() {
        if(!is_dir($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_dir($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder()."arenas");
        }
        if(!is_dir($this->getDataFolder()."arenas/default")) {
            @mkdir($this->getDataFolder()."arenas/default");
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            EggWars::getInstance()->saveResource("/config.yml");
        }
        $this->mainConfigData = $this->getConfig()->getAll();
    }
}