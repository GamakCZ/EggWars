<?php

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