<?php

/*
 *    _____                  __        __
 *   | ____|   __ _    __ _  \ \      / /   __ _   _ __   ___
 *   |  _|    / _` |  / _` |  \ \ /\ / /   / _` | | '__| / __|
 *   | |___  | (_| | | (_| |   \ V  V /   | (_| | | |    \__ \
 *   |_____|  \__, |  \__, |    \_/\_/     \__,_| |_|    |___/
 *           |___/   |___/
 */

declare(strict_types=1);

namespace vixikhd\eggwars\arena\scheduler;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;
use vixikhd\eggwars\scheduler\EggWarsTask;

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