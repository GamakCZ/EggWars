<?php

/**
 *    Copyright 2018-2019 GamakCZ
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace vixikhd\eggwars\arena\listener;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\arena\shop\CustomChestInventory;
use vixikhd\eggwars\arena\team\Team;
use vixikhd\eggwars\EggWars;
use vixikhd\eggwars\position\EggWarsPosition;
use vixikhd\eggwars\utils\Color;
use pocketmine\block\Block;
use pocketmine\entity\Villager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\inventory\InventoryPickupArrowEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
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
     * @event
     *
     * @param PlayerChatEvent $event
     */
    public function onChat(PlayerChatEvent $event) {
        $player = $event->getPlayer();

        if(!$this->getArena()->inGame($player)) {
            return;
        }

        $msg = $event->getMessage();

        if(!$this->getArena()->getTeamByPlayer($player) instanceof Team) {
            $this->getArena()->broadcastMessage("§8[§5Lobby§8]§7 {$player->getName()}: $msg");
            $event->setCancelled(true);
            return;
        }

        $team = $this->getArena()->getTeamByPlayer($player);
        $args = str_split($msg);

        if($args[0] == "!") {
            array_shift($args);
            $this->getArena()->broadcastMessage($team->getMinecraftColor()."[ALL] §7".$player->getName().": ".implode("", $args));
            $event->setCancelled(true);
            return;
        }

        $team->broadcastMessage($team->getMinecraftColor()."[Team] §7".$player->getName().": ".$msg);
        $event->setCancelled(true);

        return;
    }

    /**
     * @event
     *
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();

        if(!$this->getArena()->inGame($player)) {

            $signPos = EggWarsPosition::fromArray($this->getArena()->arenaData["sign"],  $this->getArena()->arenaData["sign"][3]);
            $sign = $signPos->getLevel()->getTile($signPos->asVector3());

            if($sign instanceof Sign && $this->getArena()->getPhase() == 0) {
                if($event->getBlock()->asVector3()->equals($signPos->asVector3())) {
                    $this->getArena()->joinPlayer($event->getPlayer());
                }
            }

            return;
        }

        if($this->getArena()->getPhase() == 0) {
            if($event->getAction() != $event::RIGHT_CLICK_AIR) {
                return;
            }

            $item = $player->getInventory()->getItemInHand();

            if($item->getId() == 0) {
                return;
            }

            if(!is_string($mc = Color::getMCFromId("{$item->getId()}:{$item->getDamage()}"))) {
                return;
            }

            $team = $this->getArena()->getTeamByMinecraftColor($mc);

            if(!$team instanceof Team) {
                if(!is_string($mc = Color::getMCFromId("{$item->getId()}:{$item->getDamage()}", 1))) {
                    return;
                }

                $team = $this->getArena()->getTeamByMinecraftColor($mc);
            }

            $this->getArena()->addPlayerToTeam($player, $team->getTeamName());
            return;
        }
        if($event->getBlock()->getId() == Item::DRAGON_EGG) {
            $event->setCancelled($bool = $this->getArena()->teamManager->onEggBreak($player, $event->getBlock()->asVector3()));
            if(!$bool) {
                $event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(0));
            }
        }
    }

    public function onPickup(InventoryPickupArrowEvent $event) {
        foreach($event->getInventory()->getViewers() as $player) {
            if($player instanceof Player && $this->getArena()->inGame($player)) {
                $event->setCancelled(true);
                $event->getArrow()->close();
            }
        }
    }

    /**
     * @event
     *
     * @param EntityDamageEvent $event
     */
    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();

        if($entity instanceof Villager) {
            if($event instanceof EntityDamageByEntityEvent) {

                /** @var Player $damager */
                $damager = $event->getDamager();
                $this->getArena()->shopManager->openShop($damager, $this->getArena()->getTeamByPlayer($damager));
                $event->setCancelled(true);
            }

            return;
        }

        if(!$entity instanceof Player) {
            return;
        }

        if(!$this->getArena()->inGame($entity)) {
            return;
        }

        if($this->getArena()->getPhase() === 0) {
            if($event->getCause() == $event::CAUSE_VOID) {
                $entity->teleport(EggWarsPosition::fromArray($this->getArena()->arenaData["lobby"], $this->getArena()->arenaData["lobby"][3]));
            }
            $event->setCancelled(true);
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if($damager instanceof Player) {
                if($this->getArena()->inGame($damager)) {
                    if(strcasecmp($this->getArena()->getTeamByPlayer($damager)->getTeamName(), $this->getArena()->getTeamByPlayer($entity)->getTeamName()) === 0) {
                        $event->setCancelled(true);
                    }
                }
            }
        }

        if(!($entity->getHealth()-$event->getDamage() <= 0)) {
            return;
        }

        $event->setCancelled(true);

        if((!$event instanceof EntityDamageByEntityEvent) && $event->getCause() == EntityDamageByEntityEvent::CAUSE_VOID) {
            $this->deathManager->onVoidDeath($entity);
            $this->deathManager->callEvent($entity, $event);
            return;
        }

        if($event->getCause() == EntityDamageByEntityEvent::CAUSE_FIRE || $event->getCause() == EntityDamageByEntityEvent::CAUSE_FIRE_TICK) {
            $this->deathManager->onBurnDeath($entity);
            $this->deathManager->callEvent($entity, $event);
            return;
        }

        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();

            if(!$damager instanceof Player) {
                $this->deathManager->onBasicDeath($entity);
                $this->deathManager->callEvent($entity, $event);
                return;
            }

            $this->deathManager->onDeath($entity, $damager);
            $this->deathManager->callEvent($entity, $event);
            return;
        }

        $this->deathManager->onBasicDeath($entity);
        $this->deathManager->callEvent($entity, $event);
    }

    /**
     * @event
     *
     * @param DataPacketReceiveEvent $event
     */
    public function onWindowClose(DataPacketReceiveEvent $event) {
        $pk = $event->getPacket();

        if($pk instanceof ContainerClosePacket) {
            $player = $event->getPlayer();

            if($this->getArena()->inGame($player)) {
                $packet = new UpdateBlockPacket();
                $packet->x = intval($player->getX());
                $packet->y = intval($player->getY())+4;
                $packet->z = intval($player->getZ());
                $packet->blockData = UpdateBlockPacket::FLAG_ALL;
                $packet->blockId = $player->getLevel()->getBlock($player->add(0, 4))->getId();
                $player->dataPacket($packet);

            }
        }
    }

    /**
     * @event
     *
     * @param InventoryTransactionEvent $event
     */
    public function onTransaction(InventoryTransactionEvent $event) {

        $transaction = $event->getTransaction();

        /** @var CustomChestInventory $chestInventory */
        $chestInventory = null;

        foreach($transaction->getInventories() as $inventory) {
            if($inventory instanceof CustomChestInventory) {
                $chestInventory = $inventory;
            }
        }

        if($chestInventory === null) {
            return;
        }

        /** @var Player $player */
        $player = null;

        foreach ($chestInventory->getViewers() as $viewer) {
            if($viewer instanceof Player) {
                $player = $viewer;
            }
        }

        /** @var Item $targetItem */
        $targetItem = null;

        /**
         * @var InventoryAction $inventoryAction
         */
        foreach ($transaction->getActions() as $inventoryAction) {
            if($inventoryAction->getTargetItem()->getId() !== Item::AIR) {
                $targetItem = $inventoryAction->getTargetItem();
            }
        }


        if($targetItem === null || $targetItem->getId() == 0) {
            $event->setCancelled(true);
            return;
        }

        $slot = -1;
        foreach ($chestInventory->getContents() as $chestSlot => $chestItem) {
            if($chestItem->getId() == $targetItem->getId() && $chestItem->getDamage() == $targetItem->getDamage() && $chestItem->getCount() == $targetItem->getCount() && $chestItem->getCustomName() == $targetItem->getCustomName()) {
                if($slot == -1) {
                    $slot = $chestSlot;
                }
            }
        }

        if($slot == -1) {
            $event->setCancelled(true);
            return;
        }


        // BROWSING
        if($slot <= 8) {
            $this->getArena()->shopManager->onBrowseTransaction($player, $chestInventory, $slot);
        }

        // BUYING
        else {
            $this->getArena()->shopManager->onBuyTransaction($player, $targetItem, $slot);
        }

        $event->setCancelled(true);
    }



    /**
     * @event
     *
     * @param PlayerExhaustEvent $event
     */
    public function onExhaust(PlayerExhaustEvent $event) {
        $player = $event->getPlayer();
        if(!$player instanceof Player) {
            return;
        }
        if(($this->getArena()->getPhase() == 0) && $this->getArena()->inGame($player)) {
            $player->setFood(20);
            $event->setCancelled();
        }
    }

    /**
     * @event
     *
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }
        if($this->getArena()->inGame($entity)) {
            if($event->getTarget()->getId() != $this->getArena()->getLevel()->getId()) {
                $this->arena->disconnectPlayer($entity);
            }
        }
        if($this->getArena()->getPhase() == 1) {
            if($this->getArena()->inGame($entity)) {
                if($event->getTarget()->getName() != $this->getArena()->getLevel()->getName()) {
                    $this->getArena()->disconnectPlayer($entity);
                }
            }
        }
    }

    /**
     * @event
     *
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        if(!$this->getArena()->inGame($player)) {
            return;
        }
        if($event->getBlock()->getId() == Item::DRAGON_EGG) {
            $bool = $this->getArena()->teamManager->onEggBreak($player, $event->getBlock()->asVector3());
            if(!$bool) {
                $event->getBlock()->getLevel()->setBlock($event->getBlock()->asVector3(), Block::get(0));
            }
            $event->setCancelled($bool);
        }
        /** @var array $ids */
        $blocks = $this->getArena()->shopManager->getBreakableBlocks();

        if(!in_array($event->getBlock()->getId(), $blocks)) {
            $event->setCancelled();
        }

        if($event->getBlock()->getId() == Item::SPONGE) {
            #$event->setDrops($this->getArena()->shopManager->randomItem());
        }
    }

    /**
     * @event
     *
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event) {
        /** @var array $ids */
        $blocks = $this->getArena()->shopManager->getBreakableBlocks();

        if(!$this->getArena()->inGame($event->getPlayer())) {
            return;
        }

        if(!in_array($event->getBlock()->getId(), $blocks)) {
            $event->setCancelled();
        }
    }

    public function onQuit(PlayerQuitEvent $event) {
        if($this->getArena()->inGame($event->getPlayer())) {
            $this->getArena()->disconnectPlayer($event->getPlayer(), 1);
        }
    }

    /**
     * @api
     *
     * @return EggWars $plugin
     */
    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }

    /**
     * @api
     *
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}
