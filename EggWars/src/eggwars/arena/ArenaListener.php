<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use eggwars\position\EggWarsVector;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

/**
 * Class ArenaListener
 * @package eggwars\arena
 */
class ArenaListener implements Listener {

    /**
     * @var Arena $arena
     */
    private $arena;

    /**
     * ArenaListener constructor.
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if($event->getBlock()->getId() != Block::DRAGON_EGG) {
            return;
        }
        if(!$this->getArena()->inGame($player)) {
            $event->setCancelled(true);
            return;
        }
        $team = $this->getArena()->getTeamEggByVector($event->getBlock()->asVector3());
        if($team instanceof Team) {
            $event->setDrops([]);
            $this->getArena()->broadcastMessage($team->getColor().$team->getTeamName()."§7 was removed by ".$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7!");
            return;
        }
    }

    /**
     * @return EggWars $eggWars
     */
    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}