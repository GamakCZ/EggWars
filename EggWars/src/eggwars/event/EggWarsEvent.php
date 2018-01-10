<?php

declare(strict_types=1);

namespace eggwars\event;

use eggwars\EggWars;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\plugin\Plugin;

/**
 * Class EggWarsEvent
 * @package event
 */
abstract class EggWarsEvent extends PluginEvent {

    /**
     * EggWarsEvent constructor.
     */
    public function __construct() {
        parent::__construct(EggWars::getInstance());
    }

    /**
     * @return Plugin|EggWars $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}