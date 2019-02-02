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

use vixikhd\eggwars\event\PlayerArenaDeathEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Position;
use pocketmine\Player;

class DeathManager {

    /** @var ArenaListener $arenaListener */
    public $arenaListener;

    /**
     * DeathManager constructor.
     * @param ArenaListener $arenaListener
     */
    public function __construct(ArenaListener $arenaListener) {
        $this->arenaListener = $arenaListener;
    }

    public function onBasicDeath(Player $player) {
        $this->arenaListener->getArena()->broadcastMessage($this->getPlayerColor($player).$player->getName()." §7death.");
        $this->respawn($player);
    }

    /**
     * @param Player $player
     */
    public function onVoidDeath(Player $player) {
        $this->arenaListener->getArena()->broadcastMessage($this->getPlayerColor($player).$player->getName()." §7fell into void.");
        $this->respawn($player);
    }

    /**
     * @param Player $player
     */
    public function onBurnDeath(Player $player) {
        $this->arenaListener->getArena()->broadcastMessage($this->getPlayerColor($player).$player->getName()." §7burned.");
        $this->respawn($player);
    }

    /**
     * @param Player $player
     * @param Player $damager
     */
    public function onVoidThrowDeath(Player $player, Player $damager) {
        $this->arenaListener->getArena()->broadcastMessage($this->getPlayerColor($player).$player->getName()." §7was knocked into the void by ".$this->getPlayerColor($damager).$damager->getName());
        $this->respawn($player);
    }

    /**
     * @param Player $player
     * @param Player $damager
     */
    public function onDeath(Player $player, Player $damager) {
        $this->arenaListener->getArena()->broadcastMessage($this->getPlayerColor($player).$player->getName()." §7was killed by ".$this->getPlayerColor($damager).$damager->getName());
        $this->respawn($player);
    }

    /**
     * @param Player $player
     * @return string $color
     */
    public function getPlayerColor(Player $player): string {
        return $this->arenaListener->getArena()->getTeamByPlayer($player)->getMinecraftColor();
    }

    /**
     * @param Player $player
     */
    public function respawn(Player $player) {
        if(!$this->arenaListener->getArena()->getTeamByPlayer($player)->isAlive()) {
            $this->arenaListener->getArena()->disconnectPlayer($player);
            $player->addTitle("§cYOU LOST!");
            return;
        }
        $player->setHealth(20);
        $player->setFood(20);
        $player->setGamemode($player::SURVIVAL);
        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();
        $player->teleport(Position::fromObject($this->arenaListener->getArena()->getTeamSpawnVector($this->arenaListener->getArena()->getTeamByPlayer($player)->getTeamName()), $this->arenaListener->getArena()->getLevel()));
    }

    public function callEvent(Player $player, EntityDamageEvent $lastDmg) {
        $this->arenaListener->getPlugin()->getServer()->getPluginManager()->callEvent(new PlayerArenaDeathEvent($player, $this->arenaListener->getArena(), $lastDmg));
    }
}