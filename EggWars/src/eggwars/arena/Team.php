<?php

declare(strict_types = 1);

namespace eggwars\arena;

use pocketmine\Player;

/**
 * Class Team
 * @package eggwars\arena
 */
class Team {

    /** @var Player[] $players */
    public $players = [];

    /** @var  string $name */
    public $name;

    /** @var bool $alive */
    public $alive = true;

    /** @var int $int */
    public $int;

    /**
     * Team constructor.
     * @param string $name
     * @param Player[] $players
     */
    public function __construct(string $name, int $int, array $players) {
        array_merge($this->players, $players);
        $this->name = $name;
        $this->int = $int;
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
}