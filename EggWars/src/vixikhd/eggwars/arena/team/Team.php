<?php

/*
 *    _____                  __        __
 *   | ____|   __ _    __ _  \ \      / /   __ _   _ __   ___
 *   |  _|    / _` |  / _` |  \ \ /\ / /   / _` | | '__| / __|
 *   | |___  | (_| | | (_| |   \ V  V /   | (_| | | |    \__ \
 *   |_____|  \__, |  \__, |    \_/\_/     \__,_| |_|    |___/
 *           |___/   |___/
 */

declare(strict_types = 1);

namespace vixikhd\eggwars\arena\team;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;
use pocketmine\math\Vector3;
use pocketmine\Player;

/**
 * Class Team
 * @package eggwars\arena
 */
class Team {

    /**
     * @var Player[] $players
     */
    public $players = [];

    /**
     * @var  string $name
     */
    public $name;

    /**
     * @var string $color
     */
    public $color;

    /**
     * @var Vector3 $spawn
     */
    public $spawn;

    /**
     * @var bool $alive
     */
    public $alive = true;

    /**
     * @var TeamManager $teamManager
     */
    public $teamManager;

    /**
     * Team constructor.
     * @param string $name
     * @param Player[] $players
     */
    public function __construct(TeamManager $teamManager, string $name, string $color, array $players) {
        array_merge($this->players, $players);
        $this->name = $name;
        $this->color = $color;
        $this->teamManager = $teamManager;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        if(!$this->isFull()) {
            $this->players[$player->getName()] = $player;
        }
    }

    public function removePlayer(Player $player) {
        unset($this->players[$player->getName()]);
    }

    /**
     * @return bool $return
     */
    public function isFull():bool {
        return boolval(count($this->getTeamsPlayers()) >= $this->getArena()->arenaData["playersPerTeam"]);
    }

    /**
     * @return bool $alive
     */
    public function isAlive(): bool {
        return $this->alive;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function inTeam(Player $player): bool {
        $return = false;
        foreach ($this->getTeamsPlayers() as $teamsPlayer) {
            if($player->getName() == $teamsPlayer->getName()) {
                $return = true;
            }
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string {
        return $this->color.$this->name;
    }

    /**
     * @return string $color
     */
    public function getMinecraftColor():string {
        return $this->color;
    }

    /**
     * @param bool $alive
     */
    public function setAlive($alive = false) {
        $this->alive = $alive;
    }

    /**
     * @param Vector3 $vector3
     */
    public function setSpawn(Vector3 $vector3) {
        $this->spawn = $vector3;
    }

    /**
     * @return Player[]
     */
    public function getTeamsPlayers() {
        $players = [];
        foreach ($this->players as $name => $player) {
            array_push($players, $player);
        }
        return $players;
    }

    /**
     * @return int
     */
    public function getPlayersCount(): int {
        return intval(count($this->players));
    }

    /**
     * @return string
     */
    public function getTeamName() {
        return $this->name;
    }

    public function reload() {
        if(count($this->getTeamsPlayers()) <= 0) {
            $this->setAlive(false);
        }
    }

    /**
     * @param string $msg
     */
    public function broadcastMessage(string $msg) {
        foreach ($this->getTeamsPlayers() as $player) {
            $player->sendMessage($msg);
        }
    }

    /**
     * @return EggWars $eggWars
     */
    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }

    /**
     * @return TeamManager $teamManager
     */
    public function getTeamManager(): TeamManager {
        return $this->teamManager;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->getTeamManager()->getArena();
    }
}