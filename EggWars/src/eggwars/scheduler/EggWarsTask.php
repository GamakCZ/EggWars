<?php

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
    abstract function getPlugin():EggWars;

    /**
     * @return Arena $arena
     */
    abstract function getArena(): Arena;
}