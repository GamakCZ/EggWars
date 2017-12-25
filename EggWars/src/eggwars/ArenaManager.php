<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\arena\Arena;
use eggwars\utils\ConfigManager;
use pocketmine\Player;
use pocketmine\utils\Config;

/**
 * Class ArenaManager
 * @package eggwars
 */
class ArenaManager extends ConfigManager {

    /**
     * @var Arena[] $arenas
     */
    public $arenas = [];

    public function __construct() {
        $this->initConfig();
        $this->loadArenas();
    }

    /**
     * @param string $name
     * @return Arena $arena
     */
    public function createArena(string $name) {
        try {
            if($this->arenaExists($name)) {
                $this->getPlugin()->getLogger()->critical("Arena already exists!");
                return null;
            }
            file_put_contents($this->getDataFolder()."arenas/".$name.".yml", $this->getPlugin()->getResource("arenas/default.yml"));
            return $this->arenas[$name] = new Arena($this->getPlugin(), new Config($this->getArenaDataFolder()."/".$name.".yml", Config::YAML));
        }
        catch (\Exception $exception) {
            $this->getPlugin()->getLogger()->critical($exception->getMessage()." / ".$exception->getLine()." / ".$exception->getCode());
        }
    }

    /**
     * @param string $name
     * @return bool $bool
     */
    public function arenaExists(string $name):bool {
        return boolval(isset($this->arenas[$name]));
    }

    /**
     * @param string $name
     * @return Arena
     */
    public function getArenaByName(string $name):Arena {
        return $this->arenas[$name];
    }

    /**
     * @param Player $player
     * @return Arena
     */
    public function getArenaByPlayer(Player $player):Arena {
        $arena = null;
        foreach ($this->arenas as $arenas) {
            if($arenas->inGame($player)) {
                $arena = $arenas;
            }
        }
        return $arena;
    }

    /**
     * @param bool $force
     */
    public function saveArenas($force = false) {
        /**
         * @var string $name
         * @var Arena $arena
         */
        foreach ($this->arenas as $name => $arena) {
            $config = new Config($this->getArenaDataFolder().$name.".yml", Config::YAML);
            $config->setAll($arena->arenaData);
            $config->save();
            $this->getPlugin()->getLogger()->notice("Arena {$name} is successfully saved!");
        }
    }

    /**
     * @param bool $reload
     */
    private function loadArenas($reload = false) {
        foreach (glob($this->getArenaDataFolder()."/*.yml") as $file) {
            $this->loadArena($file);
        }
    }

    /**
     * @param string $configPath
     */
    private function loadArena(string $configPath) {
        $this->arenas[basename($configPath, ".yml")] = new Arena($this->getPlugin(), new Config($configPath, Config::YAML));
    }

    /**
     * @return EggWars $plugin
     */
    public function getPlugin() {
        return EggWars::getInstance();
    }
}