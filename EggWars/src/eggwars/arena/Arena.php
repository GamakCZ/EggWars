<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

/**
 * Class Arena
 * @package eggwars\arena
 */
class Arena {

    /**
     * @var array $arenaData
     */
    public $arenaData = [];

    /**
     * @var array $progress
     */
    private $progress = [];

    /**
     * @var int $time
     */
    public $time, $phase;

    /**
     * @var Player[] $players
     */
    public $players = [];

    /**
     * @var  Team[] $teams
     */
    public $teams = [];

    /**
     * @var Task $scheduler
     */
    private $scheduler;

    /**
     * @var Listener $listener
     */
    private $listener;

    /**
     * Arena constructor.
     * @param EggWars $eggWars
     * @param Config $config
     */
    public function __construct(EggWars $eggWars, Config $config) {
        $this->arenaData = $config->getAll();
        $this->loadGame();
    }

    private function loadGame() {
        $this->loadTeams();
    }

    private function loadLevel() {

    }

    private function loadTeams() {
        foreach ($this->arenaData["teams"] as $team) {
            $color = strval($team["color"]);
            $this->teams[$team] = new Team($team, $color, []);
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function inGame(Player $player) : bool {
        $return = false;
        foreach ($this->players as $players) {
            if($players->getName() == $player->getName()) {
                $return = true;
            }
        }
        return $return;
    }
}
