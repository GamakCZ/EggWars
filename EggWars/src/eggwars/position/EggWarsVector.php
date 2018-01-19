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

use pocketmine\math\Vector3;

/**
 * Class EggWarsVector
 * @package eggwars\math
 */
class EggWarsVector extends Vector3 {

    /**
     * @param array $array
     * @return EggWarsVector
     */
    public static function fromArray(array $array): EggWarsVector {
        return new EggWarsVector($array[0], $array[1], $array[2]);
    }
}