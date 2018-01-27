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
     * @return EggWarsPosition|null
     */
    public static function fromArray(array $array, string $level) {
        if(!Server::getInstance()->isLevelGenerated($level)) {
            return null;
        }
        return new EggWarsPosition($array[0], $array[1], $array[2], Server::getInstance()->getLevelByName($level));
    }
}