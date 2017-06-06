<?php

namespace EggWars\Event;

use EggWars\EggWars;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {

    /** @var  EggWars */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }
}
