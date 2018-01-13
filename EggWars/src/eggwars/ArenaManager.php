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
        if($this->arenaExists($name)) {
            $this->getPlugin()->getLogger()->critical("Arena already exists!");
            return null;
        }

        // config
        $arenaConfig = new Config($this->getDataFolder()."arenas/$name.yml", Config::YAML, $this->defaultArenaData);
        $arenaConfig->save();

        return $arena = $this->arenas[$name] = new Arena($this->getPlugin(), $arenaConfig);

    }

    public function removeArena(string $name) {
        if(!$this->arenaExists($name)) {
            return;
        }
        unset($this->arenas[$name]);
        unlink($this->getDataFolder()."arenas/$name.yml");
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
     * @return Arena|bool
     */
    public function getArenaByPlayer(Player $player) {
        $arena = false;
        foreach ($this->arenas as $arenas) {
            if($arenas->inGame($player)) {
                $arena = $arenas;
            }
        }
        return $arena;
    }

    /**
     * @return string $list
     */
    public function getListArenasInString(): string {
        $list = [];
        foreach ($this->arenas as $name => $arena) {
            $name = $arena->isEnabled() ? "§a$name" : "§c$name";
            array_push($list, $name);
        }
        return implode(", ", $list);
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
            if(is_file($this->getArenaDataFolder()."/".$name.".yml")) {
                $config = new Config($this->getArenaDataFolder()."/".$name.".yml", Config::YAML);
                $config->setAll($arena->arenaData);
                $config->save();
            }
            else {
                $config = new Config($this->getArenaDataFolder()."/".$name.".yml", Config::YAML, $arena->arenaData);
                $config->save();
            }
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