<?php

declare(strict_types=1);

namespace vixikhd\eggwars\event;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

class PlayerArenaJoinEvent extends PluginEvent {

    public static $handlerList = \null;

    /** @var Player $player */
    protected $player;

    /** @var Arena $arena */
    protected $arena;

    /**
     * PlayerArenaJoinEvent constructor.
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
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}