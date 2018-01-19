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

namespace eggwars\scheduler;

use eggwars\arena\Arena;
use eggwars\EggWars;
use pocketmine\scheduler\Task;

/**
 * Class EggWarsTask
 * @package eggwars\scheduler
 */
abstract class EggWarsTask extends Task {

    /**
     * @return EggWars $eggWars
     */
    abstract function getPlugin(): EggWars;

    /**
     * @return Arena $arena
     */
    abstract function getArena(): Arena;
}