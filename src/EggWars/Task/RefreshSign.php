<?php

namespace EggWars\Task;

use EggWars\EggWars;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class RefreshSign extends PluginTask {

    /** @var EggWars */
    public $plugin;

    /** @var  Task */
    public $task;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->task = $this->plugin->task;
        parent::__construct($plugin);
    }

    public function getServer() {
        return $this->plugin->getServer();
    }

    public function onRun($currentTick) {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getTiles() as $tile) {
                if($tile instanceof Sign) {
                    if($tile->getText()[0] == "EW" && $tile->getText()[1] == "joinsign") {
                        $id = $this->task->getArena()->addArena();
                        $tile->setText($this->plugin->getConfig()->get("signprefix"));
                    }
                }
            }
        }
    }
}
