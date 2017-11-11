<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use pocketmine\Player;
use pocketmine\utils\Config;

/**
 * Class Arena
 * @package eggwars\arena
 */
class Arena {

    private $task;
    private $listener;

    /** @var  int $phase */
    public $phase;
    /** @var  int $startTime */
    public $startTime;
    /** @var  int $gameTime */
    public $gameTime;
    /** @var  int $restartTime */
    public $restartTime;

    /** @var array $arenaData */
    public $arenaData;

    /** @var EggWars $plugin */
    public $plugin;

    /** @var  Player[] $players */
    public $players;
    /** @var  Team[] $teams */
    public $teams;

    /**
     * Arena constructor.
     * @param EggWars $eggWars
     * @param Config $config
     */
    public function __construct(EggWars $eggWars, Config $config) {
        $this->plugin = $eggWars;
        $this->arenaData = $config->getAll();
        $this->restart();
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function inGame(Player $player) : bool {
        return in_array($player->getName(), $this->players);
    }

    public function restart() {
        $this->players = [];
        $this->phase = 0;
        $this->startTime = intval($this->arenaData["startTime"]);
        $this->gameTime = intval($this->arenaData["maxGameTime"]);
        $this->restartTime = intval($this->arenaData["restartTime"]);
        $this->reloadTeams();
    }

    public function reloadTeams() {
        $teamInt = 0;
        foreach ($this->arenaData["teams"] as $teamName => $teamData) {
            $this->teams[$teamName] = new Team($teamName, $teamInt, []);
            $teamInt++;
        }
    }

    public function shortStartTime(Player $player) {
        
    }

    public function startGame() {

    }

    public function teleportToGame(Player $player) {

    }

    public function unloadArena() {

    }
}
