<?php

declare(strict_types=1);

namespace eggwars\arena\listener;

use eggwars\arena\Arena;
use eggwars\arena\team\Team;
use eggwars\EggWars;
use eggwars\position\EggWarsPosition;
use eggwars\utils\Color;
use pocketmine\block\Block;
use pocketmine\entity\Villager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\tile\Sign;

/**
 * Class ArenaListener
 * @package eggwars\arena\listener
 */
class ArenaListener implements Listener {

    /** @var Arena $arena */
    private $arena;

    /** @var DeathManager $deathManager */
    public $deathManager;

    /**
     * ArenaListener constructor.
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
        $this->deathManager = new DeathManager($this);
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onArenaJoin(PlayerInteractEvent $event) {
        $signPos = EggWarsPosition::fromArray($this->getArena()->arenaData["sign"],  $this->getArena()->arenaData["sign"][3]);
        $sign = $signPos->getLevel()->getTile($signPos->asVector3());
        if($sign instanceof Sign) {
            if($event->getBlock()->asVector3()->equals($signPos->asVector3())) {
                $this->getArena()->joinPlayer($event->getPlayer());
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onTeamJoin(PlayerInteractEvent $event) {
        if(!$this->getArena()->inGame($event->getPlayer())) {
            return;
        }
        if($event->getAction() !== $event::RIGHT_CLICK_AIR) {
            return;
        }
        $item = $event->getPlayer()->getInventory()->getItemInHand();
        if($item->getId() == 0) {
            return;
        }
        if(!is_string($mc = Color::getMCFromId("{$item->getId()}"))) {
            return;
        }
        $team = $this->getArena()->getTeamByMinecraftColor($mc);
        $this->getArena()->addPlayerToTeam($event->getPlayer(), $team->getTeamName());
    }

    /**
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }
        if(!$this->getArena()->inGame($entity)) {
            return;
        }
        if(!($entity->getHealth()-$event->getFinalDamage() <= 0)) {
            return;
        }
        $event->setCancelled(true);
        if((!$event instanceof EntityDamageByEntityEvent) && $event->getCause() == EntityDamageByEntityEvent::CAUSE_VOID) {
            $this->deathManager->onVoidDeath($entity);
            return;
        }
        if($event->getCause() == EntityDamageByEntityEvent::CAUSE_FIRE || $event->getCause() == EntityDamageByEntityEvent::CAUSE_FIRE_TICK) {
            $this->deathManager->onBurnDeath($entity);
            return;
        }
        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if(!$damager instanceof Player) {
                $this->deathManager->onBasicDeath($entity);
                return;
            }
            $this->deathManager->onDeath($entity, $damager);
            return;
        }
        $this->deathManager->onBasicDeath($entity);
    }



    /**
     * @param PlayerExhaustEvent $event
     */
    public function onExhaust(PlayerExhaustEvent $event) {
        $player = $event->getPlayer();
        if(!$player instanceof Player) {
            return;
        }
        if(($this->getArena()->getPhase() == 0 || $this->getArena()->getPhase() == 1) && $this->getArena()->inGame($player)) {
            $event->setCancelled();
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onEggBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if($this->getArena()->inGame($player) && $event->getBlock()->getId() == Item::DRAGON_EGG) {
            $bool = $this->getArena()->teamManager->onEggBreak($player, $event->getBlock()->asVector3());
            if($bool == false) {
                $event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(0));
            }
            $event->setCancelled($bool);
        }
    }

    /**
     * @param PlayerInteractEvent $event
     */
    public function onEggInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        if($this->getArena()->inGame($player) && $event->getBlock()->getId() == Item::DRAGON_EGG) {
            $event->setCancelled($bool = $this->getArena()->teamManager->onEggBreak($player, $event->getBlock()->asVector3()));
            if($bool = false) {
                $event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(0));
            }
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