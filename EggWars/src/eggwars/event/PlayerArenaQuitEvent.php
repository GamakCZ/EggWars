<?php

declare(strict_types=1);

namespace eggwars\event;

use eggwars\arena\Arena;
use eggwars\EggWars;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

/**
 * Class PlayerArenaQuitEvent
 * @package eggwars\event
 */
class PlayerArenaQuitEvent extends PluginEvent {

    public static $handlerList = \null;

    /** @var Player $player */
    protected $player;

    /** @var Arena $arena */
    protected $arena;

    /**
     * PlayerArenaQuitEvent constructor.
     * @param Player $player
     * @param Arena $arena
     */
    public function __construct(Player $player, Arena $arena) {
        $this->player = $player;
        $this->arena = $arena;
        parent::__construct(EggWars::getInstance());
    }

    /**
     * @return Player $player
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * @return Arena $arena
     */
    public function getArena() {
        return $this->arena;
    }
}