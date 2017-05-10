<?php

namespace EggWars\Task;

use EggWars\Arena\Arena;
use EggWars\EggWars;
use pocketmine\utils\Config;

class Task {

    /** @var  EggWars */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function registerTasks() {
        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new RefreshSign($this), 20);
        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new ItemSpawn($this), 20);
    }

    /**
     * @return Arena
     */
    public function getArena() {
        return $this->plugin->arena;
    }

    /**
     * @return Config
     */
    public function getConfig() {
        return $this->plugin->getConfig();
    }
}
