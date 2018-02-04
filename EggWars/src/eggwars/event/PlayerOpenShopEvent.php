<?php

declare(strict_types=1);

namespace eggwars\event;

use eggwars\arena\Arena;
use eggwars\EggWars;
use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

/**
 * Class PlayerOpenShopEvent
 * @package eggwars\event
 */
class PlayerOpenShopEvent extends PluginEvent implements Cancellable {

    public static $handlerList = \null;

    const SHOP_TYPE_CUSTOM = -1;
    const SHOP_TYPE_CHEST = 0;

    /** @var Player $player */
    protected $player;

    /** @var Arena $arena */
    protected $arena;

    /** @var int $shopType */
    protected $shopType = 0;

    /** @var array $shopItems */
    protected $shopItems = [];

    /**
     * PlayerOpenShopEvent constructor.
     * @param Player $player
     * @param Arena $arena
     * @param int $shopType
     * @param array $shopItems
     */
    public function __construct(Player $player, Arena $arena, int $shopType, array $shopItems) {
        $this->player = $player;
        $this->shopType = $shopType;
        $this->shopItems = $shopItems;
        parent::__construct(EggWars::getInstance());
    }

    /**
     * @return Player $player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }

    /**
     * @param int $shopType
     */
    public function setShopType(int $shopType) {
        $this->shopType = $shopType;
    }

    /**
     * @return int $shopType
     */
    public function getShopType(): int {
        return $this->shopType;
    }

    /**
     * @param array $shopItems
     */
    public function setShopItems(array $shopItems) {
        $this->shopItems = $shopItems;
    }

    /**
     * @return array $shopItems
     */
    public function getShopItems(): array {
        return $this->shopItems;
    }
}