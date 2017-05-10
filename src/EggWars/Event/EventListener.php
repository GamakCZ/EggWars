<?php

namespace EggWars\Event;

use EggWars\EggWars;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

class EventListener implements Listener {

    /** @var  EggWars */
    public $plugin;

    public $setup = [];

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function onSetup(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        foreach ($this->setup as $name => $setup) {
            if($name == $player->getName()) {

            }
        }
    }
}
