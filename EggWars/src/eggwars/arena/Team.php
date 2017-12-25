<?php

declare(strict_types = 1);

namespace eggwars\arena;

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
     * Team constructor.
     * @param string $name
     * @param Player[] $players
     */
    public function __construct(string $name, string $color, array $players) {
        array_merge($this->players, $players);
        $this->name = $name;
        $this->color = $color;
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
}