<?php

namespace EggWars\Task;

use EggWars\EggWars;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class RefreshSign extends PluginTask {

    /** @var Task */
    public $plugin;

    /**
     * RefreshSign constructor.
     * @param Task $plugin
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin->plugin);
    }

    public function getServer() {
        return $this->plugin->plugin->getServer();
    }

    public function onRun($currentTick) {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getTiles() as $tile) {
                if($tile instanceof Sign) {
                }
            }
        }
    }
}
