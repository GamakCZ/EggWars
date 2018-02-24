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

    /** @var array $mainConfigData */
    private $mainConfigData = [];

    /** @var string $prefix */
    private static $prefix;

    public function __construct() {
        $this->initConfig();
        self::$prefix = boolval($this->getFromConfig("enable-prefix")) ? $this->getFromConfig("prefix") : "§7";
    }

    /** @var array $defaultArenaData */
    public $defaultArenaData = [
        "enabled" => false,
        "name" => "",
        "startTime" => 30,
        "gameTime" => 3600,
        "restartTime" => 20,
        "teamsToStart" => 2,
        "playersPerTeam" => 2,
        "lobby" => [0, 98, 0, "world"],
        "sign" => [0, 100, 0, "world"],
        "builder" => "VixikCZ",
        "teamsCount" => 2,
        "teams" => [
        ]
    ];

    /** @var array $defaultLevelData */
    public $defaultLevelData = [
        "enabled" => false,
        "levelName" => "EggWars",
        "folderName" => "EggWars",
        "name" => "EW1level",
        "middle" => [90, 4, 90],
        "arenas" => [],
        "teams" => [
        ]
    ];

    /**
     * @api
     *
     * @param mixed $k
     * @param mixed $v
     */
    public function setToConfig($k, $v) {
        $this->mainConfigData[$k] = $v;
    }

    /**
     * @api
     *
     * @param mixed $k
     * @return mixed
     */
    public function getFromConfig($k) {
        return $this->mainConfigData[$k];
    }

    /**
     * @api
     *
     * @return string $dataFolder
     */
    public function getDataFolder():string {
        return EggWars::getInstance()->getDataFolder();
    }

    /**
     * @api
     *
     * @return string $arenaDataFolder
     */
    public function getArenaDataFolder():string {
        return EggWars::getInstance()->getDataFolder()."arenas/";
    }

    /**
     * @api
     *
     * @return Config $config
     */
    private function getConfig():Config {
        return EggWars::getInstance()->getConfig();
    }


    /**
     * @api
     *
     * @return string $dataPath
     */
    public function getDataPath():string {
        return Server::getInstance()->getDataPath();
    }

    /**
     * @api
     *
     * @return string $prefix
     */
    public static function getPrefix(): string {
        return self::$prefix;
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