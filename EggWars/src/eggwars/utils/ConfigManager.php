<?php

/*
 *    _____                __        __
 *   | ____|  __ _    __ _ \ \      / /__ _  _ __  ___
 *   |  _|   / _` | / _` |  \ \ /\ / // _` || '__|/ __|
 *   | |___ | (_| || (_| |   \ V  V /| (_| || |   \__ \
 *   |_____| \__, | \__, |    \_/\_/  \__,_||_|   |___/
 *           |___/  |___/
 */


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
        "name" => "",
        "startTime" => 30,
        "gameTime" => 600,
        "restartTime" => 20,
        "teamsToStart" => 2,
        "playersPerTeam" => 2,
        "lobby" => [0, 98, 0, "world"],
        "sign" => [0, 100, 0, "world"],
        "builder" => "VixikCZ",
        "teamsCount" => 2,
        "teams" => [
            /*"red" => [
                "color" => "§4"
            ],
            "blue" => [
                "color" => "§9"
            ]*/
        ]
    ];

    /**
     * @var array $defaultLevelData
     */
    public $defaultLevelData = [
        "enabled" => false,
        "levelName" => "EggWars",
        "folderName" => "EggWars",
        "name" => "EW1level",
        "middle" => [90, 4, 90],
        "arenas" => [],
        "teams" => [
            /*"red" => [
                "egg" => [100, 4, 100],
                "spawn" => [100, 5, 100]
            ],
            "blue" => [
                "egg" => [80, 4, 80],
                "spawn" => [80, 5, 80]
            ]*/
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
        if(!is_dir($this->getDataFolder()."levels")) {
            @mkdir($this->getDataFolder()."levels");
        }
        if(!is_dir($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder()."arenas");
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            EggWars::getInstance()->saveResource("/config.yml");
        }
        $this->mainConfigData = $this->getConfig()->getAll();
    }
}