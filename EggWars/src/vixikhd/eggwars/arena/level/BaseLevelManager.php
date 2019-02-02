<?php

declare(strict_types=1);

namespace vixikhd\eggwars\arena\level;

use pocketmine\level\Level;
use vixikhd\eggwars\arena\Arena;

/**
 * Class DefaultManager
 * @package vixikhd\eggwars\arena\level
 */
class BaseLevelManager implements LevelManager {

    /** @var Level $level */
    public $level;

    public function init(Arena $arena) {

    }

    public function getLevel(): Level {

    }

    public function chooseMap() {

    }

    public function getLevelData(): array {

    }
}