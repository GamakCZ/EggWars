<?php

declare(strict_types=1);

namespace vixikhd\eggwars\event;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\plugin\PluginEvent;
use pocketmine\Player;

/**
 * Class PlayerArenaDeathEvent
 * @package eggwars\event
 */
class PlayerArenaDeathEvent extends PluginEvent {

    public static $handlerList = \null;

    /** @var Player $player */
    protected $player;

    /** @var Arena $arena */
    protected $arena;

    /** @var EntityDamageEvent $lastDmg */
    protected $lastDmg;

    /**
     * PlayerArenaDeathEvent constructor.
     * @param Player $player
     * @param Arena $arena
     * @param EntityDamageEvent $lastDmg
     */
    public function __construct(Player $player, Arena $arena, EntityDamageEvent $lastDmg) {
        $this->player = $player;
        $this->arena = $arena;
        $this->lastDmg = $lastDmg;
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
     * @return EntityDamageEvent
     */
    public function getLastDamageCause() {
        return $this->lastDmg;
    }
}