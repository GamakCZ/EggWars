<?php

namespace EggWars\Task;

use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class ItemSpawn extends PluginTask {

    /** @var  Task */
    public $plugin;

    // Tick
    private $tick;

    public $time = [
        "Iron1" => 5,
        "Iron2" => 4,
        "Iron3" => 3,
        "Iron4" => 2,
        "Iron5" => 1,
        "Gold1" => 10,
        "Gold2" => 8.9,
        "Gold3" => 7.7,
        "Gold4" => 6.4,
        "Gold5" => 5,
        "Diamond1" => 40,
        "Diamond2" => 35,
        "Diamond3" => 30
    ];

    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($this->plugin);
    }

    public function onRun($currentTick) {
        $this->tick = 1;
        $this->tick++;
        foreach ($this->time as $text => $time) {
            foreach ($this->plugin->getArena()->runningLevels as $level) {
                foreach ($level->getTiles() as $tile) {
                    if($tile instanceof Sign) {
                        $text = $tile->getText();
                        if($text[0] == $this->plugin->getConfig()->get("signprefix")) {

                        }
                    }
                }
            }
        }
    }
}
