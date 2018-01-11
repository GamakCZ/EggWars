<?php

declare(strict_types=1);

namespace eggwars\position;

use pocketmine\level\Position;
use pocketmine\Server;

/**
 * Class EggWarsPosition
 * @package eggwars\position
 */
class EggWarsPosition extends Position {

    /**
     * @param array $array
     * @param string $level
     * @return EggWarsPosition
     */
    public static function fromArray(array $array, string $level) {
        return new EggWarsPosition($array[0], $array[1], $array[2], Server::getInstance()->getLevelByName($level));
    }
}