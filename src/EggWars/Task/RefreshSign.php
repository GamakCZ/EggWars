<?php

namespace EggWars\Task;

use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class RefreshSign extends PluginTask {

    /** @var Task */
    public $plugin;


    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($this->plugin->plugin);
    }

    public function getServer() {
        return $this->plugin->plugin->getServer();
    }

    public function onRun($currentTick) {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getTiles() as $tile) {
                if($tile instanceof Sign) {
                    if($tile->getText()[0] == "EW" && $tile->getText()[1] == "joinsign") {
                        $id = $this->plugin->getArena()->addArena();
                        $tile->setText($this->plugin->getConfig()->get("signprefix"));
                    }
                }
            }
        }
    }
}
