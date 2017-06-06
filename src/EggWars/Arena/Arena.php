<?php

namespace EggWars\Arena;

use EggWars\EggWars;
use pocketmine\level\Level;
use pocketmine\utils\Config;

class Arena {

    /** @var EggWars */
    public $plugin;

    /** @var SetupManager */
    public $setupManager;

    #public $arenas = [];

    /*public $arenas = [
        "EW-1" => [
            "players" => [
                "GamakCZ",
                "kaliiks,..."
            ],
            "status" => "ingame/lobby,...",
            "level" => "EWLobby/map"
        ]
    ];*/

    /** @param int $sid */
    private $sid;

    /** @var Level[] */
    public $runningLevels = [];

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->arenas = $this->getData()->get("arenas");
    }

    public function reloadArenas() {
        foreach ($this->arenas as $arena => $data) {
            $level = $this->plugin->getServer()->getLevelByName($data["level"]);
            foreach ($level->getPlayers() as $player) {
                $player->teleport($this->plugin->getServer()->getDefaultLevel()->getSafeSpawn(),0,0);
                $player->sendMessage(EggWars::$prefix."Reloading arenas!");
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function arenaExists($name) {
        if(in_array($name, $this->arenas)) {
            return true;
        }
    }

    /**
     * @param string $name
     */
    public function addArena($name) {
        $array = [$name => [
            "players" => [],
            "status" => "lobby",
            "map" => ""
        ]];
        array_merge($this->arenas, $array);
        $this->getData()->set("arenas", $this->arenas);
    }

    /**
     * @param Level $level
     */
    public function addMap(Level $level) {
        $array = $this->getData()->get("maps");
        array_push($array, $level->getName());
        $this->getData()->set("maps", $array);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function mapExists($name) {
        if(in_array($name, $this->getData()->get("maps"))) {
            return true;
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function levelExists($name) {
        if(is_file($this->getWorldPath()."{$name}.yml")) {
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
     * @return Config
     */
    public function getConfig() {
        return $this->plugin->getConfig();
    }

    /**
     * @return Config
     */
    public function getData() {
        return new Config($this->plugin->getDataFolder()."/data.yml", Config::YAML);
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

    /**
     * @param string $arena
     * @return array
     */
    public function getArenaPlayers($arena) {
        return $this->arenas[$arena]["players"];
    }
}
