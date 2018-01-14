<?php

declare(strict_types=1);

namespace eggwars\arena\scheduler;

use eggwars\arena\Arena;
use eggwars\EggWars;
use eggwars\scheduler\EggWarsTask;

/**
 * Class ArenaScheduler
 * @package eggwars\arena
 */
class ArenaScheduler extends EggWarsTask {

    /**
     * @var Arena $arena
     */
    public $arena;

    /**
     * @var int $tick
     */
    private $tick = 0;

    /**
     * ArenaScheduler constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $this->getArena()->progress();
    }

    /**
     * @return EggWars $eggWars
     */
    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}