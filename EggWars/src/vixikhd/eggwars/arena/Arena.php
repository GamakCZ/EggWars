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

namespace vixikhd\eggwars\arena;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\math\Vector3;
use pocketmine\tile\Tile;
use vixikhd\eggwars\arena\level\BaseLevelManager;
use vixikhd\eggwars\arena\level\LevelManager;
use vixikhd\eggwars\arena\voting\VotingManager;
use vixikhd\eggwars\EggWars;
use pocketmine\level\Position;
use pocketmine\Player;

/**
 * Class Arena
 * @package vixikhd\eggwars\arena
 */
class Arena implements Listener {

    const MSG_MESSAGE = 0;
    const MSG_TIP = 1;
    const MSG_POPUP = 2;
    const MSG_TITLE = 3;

    const PHASE_LOBBY = 0;
    const PHASE_GAME = 1;
    const PHASE_RESTART = 2;

    /** @var EggWars $plugin */
    public $plugin;

    /** @var MapReset $mapReset */
    public $mapReset;

    /** @var array $arenaData */
    public $data = [];

    /** @var bool $setup */
    public $setup = false;

    /** @var array $levels */
    public $levels = [];

    /** @var int $phase */
    public $phase = 0;

    /** @var ArenaScheduler $scheduler */
    public $scheduler;

    /** @var LevelManager $levelManager */
    public $levelManager;

    /** @var Player[] $players */
    public $players = [];

    /** @var string[] $teams */
    public $teams = [];

    /** @var int[] $aliveTeams */
    public $aliveTeams = [];

    /**
     * Arena constructor.
     * @param EggWars $plugin
     * @param array $arenaFileData
     */
    public function __construct(EggWars $plugin, array $arenaFileData) {
        $this->plugin = $plugin;
        $this->data = $arenaFileData;
        $this->setup = !$this->enable(false);

        $this->plugin->getScheduler()->scheduleRepeatingTask($this->scheduler = new ArenaScheduler($this), 20);
        $this->plugin->getServer()->getPluginManager()->registerEvents($this, $this->plugin);

        if($this->setup) {
            if(empty($this->data)) {
                $this->createBasicData();
            }
        }
        else {
            $this->loadArena();
        }
    }

    /**
     * @param bool $loadArena
     * @return bool
     */
    public function enable(bool $loadArena = true): bool {
        if(!isset($this->data["levels"])) {
            return false;
        }
        foreach ($this->data["levels"] as $index => $level) {
            if(!isset($this->plugin->levels[$level]) || !$this->plugin->levels[$level]["enabled"]) {
                unset($this->data["levels"][$index]);
            }
        }
        if(count($this->data["levels"]) == 0) {
            return false;
        }
        if($this->data["teamstostart"] == 0) {
            return false;
        }
        if($this->data["playersperteam"] == 0) {
            return false;
        }
        if(empty($this->data["lobby"])) {
            return false;
        }
        if(empty($this->data["joinsign"])) {
            return false;
        }
        if(empty($this->data["teams"])) {
            return false;
        }
        if(count($this->data["levels"]) > 1) {
            $this->levelManager = new VotingManager();
        }
        else {
            $this->levelManager = new BaseLevelManager();
        }
        $this->levelManager->init($this);
        $this->data["enabled"] = true;
        return true;
    }

    public function createBasicData() {
        $this->data = [
            "enabled" => false,
            "teamstostart" => 0,
            "playersperteam" => 0,
            "lobby" => [],
            "joinsign" => [],
            "teams" => [],
            "levels" => []
        ];
    }

    public function loadArena() {
        $this->mapReset = new MapReset($this);
        foreach ($this->data["teams"] as $team => $teamData) {
            $this->aliveTeams[$team] = true;
        }
    }

    /**
     * @param Player $player
     * @param string $team
     */
    public function joinToArena(Player $player, $team = null) {
        if(!$this->data["enabled"]) {
            $player->sendMessage("§c> Arena is under setup!");
            return;
        }

        if(count($this->players) >= $this->data["playersperteam"] * count($this->data["teams"])) {
            $player->sendMessage("§c> Arena is full!");
            return;
        }

        if($this->inGame($player)) {
            $player->sendMessage("§c> You are already in game!");
            return;
        }

        $this->players[$player->getName()] = $player;

        $player->teleport(new Position($this->data["lobby"][0], $this->data["lobby"][1], $this->data["lobby"][2], $this->plugin->getServer()->getLevelByName($this->data["lobby"][3])));

        $player->getInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        $player->getCursorInventory()->clearAll();

        $player->setGamemode($player::ADVENTURE);
        $player->setHealth(20);
        $player->setFood(20);

        foreach ($this->players as $player) {
            $player->sendMessage("§a> Player {$player->getName()} joined! §7[".count($this->players)."/".$this->data["playersperteam"] * count($this->data["teams"])."]");
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onBreak(BlockBreakEvent $event) {
        $player = $event->getPlayer();

        if($this->inGame($player) && $this->phase === self::PHASE_GAME) {
            if($event->getBlock()->getId() == Block::DRAGON_EGG) {
                $team = "";
                foreach ($this->levelManager->getLevelData()["teams"] as $t => ["spawn" => $pos]) {
                    if((new Vector3($pos[0], $pos[1], $pos[2]))->ceil()->equals($event->getBlock()->ceil())) {
                        $team = $t;
                    }
                }

                if($team == $this->teams[$player->getName()]) {
                    $player->sendMessage("§c> You cannot break your own egg.");
                    $event->setCancelled(true);
                    return;
                }

                if($team == "") {
                    return;
                }

                $this->aliveTeams[$team] = false;
                $playerTeam = $this->teams[$player->getName()];

                $this->broadcastMessage("§a> Player {$player->getName()} from "  . $this->data["teams"][$playerTeam] . $playerTeam . "§a destroyed egg of " . $this->data["teams"][$team] . $team . " §ateam.");
            }
        }
    }

    public function onExhaust(PlayerExhaustEvent $event) {
        $player = $event->getPlayer();
        if(!$player instanceof Player) {
            return;
        }

        if($this->inGame($player) && $this->phase === self::PHASE_LOBBY) {
            $event->setCancelled(true);
        }
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Player) {
            return;
        }

        if(!$this->inGame($entity)) {
            return;
        }

        if($entity->getGamemode() == $entity::SPECTATOR || $this->phase !== self::PHASE_GAME) {
            $event->setCancelled(true);
            return;
        }
    }

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        if($this->inGame($player)) {
            switch (($lastDmg = $player->getLastDamageCause())->getCause()) {
                case EntityDamageEvent::CAUSE_CONTACT:
                    if($lastDmg instanceof EntityDamageByEntityEvent && ($damager = $lastDmg->getDamager()) instanceof Player) {
                        $damager = $lastDmg->getDamager();
                        if($damager instanceof Player) {
                            $this->broadcastMessage("§a> {$this->data["teams"][$this->teams[$player->getName()]]}{$player->getName()} §awas killed by {$this->data["teams"][$this->teams[$damager]]}{$damager->getName()}§a!");
                        }
                    }
                    break;
                case EntityDamageEvent::CAUSE_PROJECTILE:
                    if($lastDmg instanceof EntityDamageByEntityEvent && ($damager = $lastDmg->getDamager()) instanceof Player) {
                        $damager = $lastDmg->getDamager();
                        if($damager instanceof Player) {
                            $this->broadcastMessage("§a> {$this->data["teams"][$this->teams[$player->getName()]]}{$player->getName()} §awas shoot by {$this->data["teams"][$this->teams[$damager]]}{$damager->getName()}§a!");
                        }
                    }
                    break;
                default:
                    $this->broadcastMessage("§a> {$this->data["teams"][$this->teams[$player->getName()]]}{$player->getName()} §adied!");
                    break;
            }
        }
    }

    /**
     * @param PlayerRespawnEvent $event
     */
    public function onRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        if(!$this->inGame($player)) return;

        $pos = $this->levelManager->getLevelData()["teams"][$this->teams[$player->getName()]]["spawn"];
        $event->setRespawnPosition(new Position($pos[0], $pos[1], $pos[2], $this->levelManager->getLevel()));

        if(!$this->aliveTeams[$this->teams[$player->getName()]]) {
            $player->setGamemode($player::SPECTATOR);
            $player->addTitle("§6You are now spectating.");
            return;
        }
    }


    /**
     * @param PlayerInteractEvent $event
     */
    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        if($this->inGame($player) && $event->getBlock()->getId() == Block::CHEST && $this->phase == self::PHASE_LOBBY) {
            $event->setCancelled(true);
            return;
        }

        if(!$block->getLevel()->getTile($block) instanceof Tile) {
            return;
        }

        $signPos = new Position($this->data["joinsign"][0], $this->data["joinsign"][1], $this->data["joinsign"][2], $this->plugin->getServer()->getLevelByName($this->data["joinsign"][3]));

        if((!$signPos->equals($block)) || $signPos->getLevel()->getId() != $block->getLevel()->getId()) {
            return;
        }

        if($this->phase == self::PHASE_GAME) {
            $player->sendMessage("§c> Arena is in-game");
            return;
        }
        if($this->phase == self::PHASE_RESTART) {
            $player->sendMessage("§c> Arena is restarting!");
            return;
        }

        if($this->setup) {
            return;
        }

        $this->joinToArena($player);
    }

    public function startGame() {
        $this->broadcastMessage("§aGame started!", self::MSG_TITLE);

        // Add players to teams
        $teamBalance = [];
        foreach (array_keys($this->data["teams"]) as $team) {
            $teamBalance[$team] = 0;
        }

        foreach ($this->players as $name => $player) {
            if(isset($this->teams[$name])) {
                $teamBalance[$this->teams[$name]]++;
            }
        }

        foreach ($this->players as $name => $player) {
            if(!isset($this->teams[$name])) {
                arsort($teamBalance);
                $teams = array_keys($teamBalance);
                $this->teams[$name] = $finalTeam = end($teams);
                $player->sendMessage("§a> You've joined to {$this->data["teams"][$finalTeam]}{$finalTeam}§a!");
            }
        }

        // Teleporting to spawns, regeneration
        foreach ($this->players as $name => $player) {
            $pos = $this->levelManager->getLevelData()["teams"][$this->teams[$name]]["spawn"];
            $player->teleport(new Position($pos[0], $pos[1], $pos[2], $this->levelManager->getLevel()));

            $player->getInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            $player->getCursorInventory()->clearAll();
            $player->setAllowFlight(false);
            $player->setXpLevel(0);
            $player->setXpProgress(0);
            $player->setFood(20);
            $player->setMaxHealth(20);
            $player->setHealth(20);

            $player->setGamemode($player::SURVIVAL);
        }

        $this->phase = self::PHASE_GAME;

    }

    public function startRestart() {

    }

    /**
     * @return bool
     */
    public function checkEnd(): bool {
        return false;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function inGame(Player $player) : bool {
        return isset($this->players[$player->getName()]);
    }

    /**
     * @param string $message
     * @param int $id
     * @param string $subMessage
     */
    public function broadcastMessage(string $message, int $id = 0, string $subMessage = "") {
        foreach ($this->players as $player) {
            switch ($id) {
                case self::MSG_MESSAGE:
                    $player->sendMessage($message);
                    break;
                case self::MSG_TIP:
                    $player->sendTip($message);
                    break;
                case self::MSG_POPUP:
                    $player->sendPopup($message);
                    break;
                case self::MSG_TITLE:
                    $player->addTitle($message, $subMessage);
                    break;
            }
        }
    }
}
