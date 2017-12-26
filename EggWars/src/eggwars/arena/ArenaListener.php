<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\level\Position;
use pocketmine\Player;

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

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        if(!$this->getArena()->inGame($player)) {
            return;
        }

        // MSG
        $lastDmg = $player->getLastDamageCause();
        if($lastDmg instanceof EntityDamageByEntityEvent) {
            $damager = $lastDmg->getDamager();
            if($damager instanceof Player && $this->getArena()->inGame($damager)) {
                $this->getArena()->broadcastMessage(EggWars::getPrefix().$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7 was killed by ".$this->getArena()->getTeamByPlayer($player)->getColor().$damager->getName()."§7!");
            }
            else {
                $this->getArena()->broadcastMessage(EggWars::getPrefix().$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7 death.");
            }
        }
        else {
            $this->getArena()->broadcastMessage(EggWars::getPrefix().$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7 death.");
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        if($this->getArena()->inGame($player)) {
            if(!$this->getArena()->getTeamByPlayer($player)->isAlive()) {
                $player->addTitle("§cYOU LOST!");
                $player->setGamemode($player::SPECTATOR);
                $player->getInventory()->clearAll();
                unset($this->getArena()->getTeamByPlayer($player)->players[$player->getName()]);
            }
        }
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
            if($this->getArena()->getTeamByPlayer($player)->getTeamName() == $this->getArena()->getTeamEggByVector($event->getBlock()->asVector3())->getTeamName()) {
                return;
            }
            $event->setDrops([]);
            $this->getArena()->broadcastMessage($team->getColor().$team->getTeamName()."§7 egg was removed by ".$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7!");
            $team->setAlive();
            return;
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event) {
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
            $event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(Block::AIR));
            $this->getArena()->broadcastMessage($team->getColor().$team->getTeamName()."§7 was removed by ".$this->getArena()->getTeamByPlayer($player)->getColor().$player->getName()."§7!");
            $team->setAlive();
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