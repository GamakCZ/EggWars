<?php

declare(strict_types = 1);

namespace eggwars\arena;

use eggwars\EggWars;
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
     * @var bool $alive
     */
    public $alive = true;

    /**
     * @var Arena $arena
     */
    public $arena;

    /**
     * Team constructor.
     * @param string $name
     * @param Player[] $players
     */
    public function __construct(Arena $arena, string $name, string $color, array $players) {
        array_merge($this->players, $players);
        $this->name = $name;
        $this->color = $color;
        $this->arena = $arena;
    }

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player) {
        if(!$this->isFull()) {
            array_push($this->players, $player);
        }
    }

    /**
     * @return bool $return
     */
    public function isFull():bool {
        return boolval(count($this->getTeamsPlayers()) >= $this->getArena());
    }

    /**
     * @return bool $alive
     */
    public function isAlive(): bool {
        return $this->alive;
    }

    /**
     * @return string $color
     */
    public function getColor():string {
        return $this->color;
    }

    /**
     * @param bool $alive
     */
    public function setAlive($alive = false) {
        $this->alive = $alive;
    }

    /**
     * @return Player[]
     */
    public function getTeamsPlayers() {
        return $this->players;
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