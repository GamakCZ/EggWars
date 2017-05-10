<?php

namespace EggWars\Arena;

class SetupManager {

    /** @var  Arena */
    public $plugin;

    // $player => $arena
    public $setup = [];

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function getPlayer() {
        return $this->plugin->plugin->getServer()->getPlayer($this->setup[0]);
    }

    public function getArena() {
        return $this->setup[$this->getPlayer()->getName()];
    }
}
