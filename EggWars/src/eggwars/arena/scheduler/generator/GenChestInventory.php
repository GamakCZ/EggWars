<?php

declare(strict_types=1);

namespace eggwars\arena\scheduler\generator;

use pocketmine\inventory\ChestInventory;
use pocketmine\tile\Tile;

/**
 * Class GenChestInventory
 * @package eggwars\arena\scheduler\generator
 */
class GenChestInventory extends ChestInventory {

    /** @var int $genLevel */
    public $genLevel;

    /** @var int $genType */
    public $genType;

    /** @var Tile $gensigntile */
    public $gensigntile;
}