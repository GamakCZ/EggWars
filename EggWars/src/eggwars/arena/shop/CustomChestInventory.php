<?php

declare(strict_types=1);

namespace eggwars\arena\shop;

use pocketmine\inventory\ChestInventory;

/**
 * Class CustomChestInventory
 * @package eggwars\arena\shop
 */
class CustomChestInventory extends ChestInventory {

    /** @var string $teamColor */
    public $teamColor = "&f";
}