<?php

/*
 *    _____                __        __
 *   | ____|  __ _    __ _ \ \      / /__ _  _ __  ___
 *   |  _|   / _` | / _` |  \ \ /\ / // _` || '__|/ __|
 *   | |___ | (_| || (_| |   \ V  V /| (_| || |   \__ \
 *   |_____| \__, | \__, |    \_/\_/  \__,_||_|   |___/
 *           |___/  |___/
 */

declare(strict_types=1);

namespace eggwars\arena\listener;

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
        $player->teleport(Position::fromObject($this->arenaListener->getArena()->getTeamSpawnVector($this->arenaListener->getArena()->getTeamByPlayer($player)->getTeamName()), $this->arenaListener->getArena()->getLevel()));
    }
}