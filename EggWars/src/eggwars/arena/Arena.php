<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use eggwars\position\EggWarsPosition;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
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
        $this->loadLevel();
        $this->scheduler = new ArenaScheduler($this);
    }

    private function loadLevel() {
        if(!Server::getInstance()->isLevelGenerated($this->arenaData["level"])) {
            $this->getPlugin()->getLogger()->critical("Arena level not found!");
            $this->getPlugin()->getPluginLoader()->disablePlugin($this->getPlugin());
        }
        else {
            Server::getInstance()->loadLevel($this->arenaData["level"]);
        }
    }

    private function loadTeams() {
        foreach ($this->arenaData["teams"] as $team => $data) {
            $color = strval($data["color"]);
            $this->teams[$team] = new Team($team, $color, []);
        }
    }

    /**
     * @return Team[] $teams
     */
    public function getAllTeams() {
        return $this->teams;
    }

    /**
     * @param string $name
     * @return Team
     */
    public function getTeamByName(string $name):Team {
        return $this->teams[$name];
    }

    /**
     * @param Player $player
     * @param string $teamName
     */
    public function addPlayerToTeam(Player $player, string $teamName) {
        array_push($this->getTeamByName($teamName)->players, $player);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function teamExists(string $name): bool {
        return isset($this->teams[$name]);
    }

    /**
     * @return Level|null $level
     */
    public function getLevel() {
        if(Server::getInstance()->isLevelGenerated($this->arenaData["level"])) {
            return Server::getInstance()->getLevelByName($this->arenaData["level"]);
        }
    }

    /**
     * @param Player $player
     * @param null $team
     */
    public function joinPlayer(Player $player, $team = null) {
        if(empty($this->progress["lobbyPlayers"]) || !is_array($this->progress["lobbyPlayers"])) {
            $this->progress["lobbyPlayers"] = [];
        }
        array_push($this->progress["lobbyPlayers"], $player);

        $player->teleport(EggWarsPosition::__fromArray($this->arenaData["lobby"], $this->arenaData["level"]));
        $player->setGamemode($player::ADVENTURE);
        $player->setHealth(20);
        $player->setFood(20);
        $player->setAllowFlight(false);
        $player->setXpProgress(0);

        $count = count($this->getAllPlayers());
        $maxCount = count($this->getAllTeams())*intval($this->arenaData["playersPerTeam"]);

        $player->sendMessage(EggWars::getPrefix()."§aYou are joined the game! §7[$count/$maxCount]");

        if($team != null) {
            if($this->teamExists(strval($team))) {
                $this->addPlayerToTeam($player, strval($team));
            }
            else {
                $this->getPlugin()->getLogger()->critical("Team {$team} was not found.");
            }
        }
    }

    /**
     * @return int $int
     */
    public function getFillTeamsCount():int {
        $count = 0;
        foreach ($this->teams as $team) {
            if(count($team->getTeamsPlayers()) > 0) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Player[] $players
     */
    public function getAllPlayers():array {
        $players = [];
        foreach ($this->teams as $team) {
            array_merge($players, $team->getTeamsPlayers());
        }
        if($this->phase <= 1) {
            array_merge($players, $this->progress["lobbyPlayers"]);
        }
        return $players;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function inGame(Player $player) : bool {
        $return = false;
        foreach ($this->getAllPlayers() as $players) {
            if($players->getName() == $player->getName()) {
                $return = true;
            }
        }
        return $return;
    }


    /**
     *
     *  PROGRESS FUNCTIONS
     *  ------------------
     *
     * 0) lobby - wait
     * 1) lobby - time to start
     * 2) game - game
     * 3) restart - restarting
     *
     */

    public function progress() {
        switch ($this->phase) {
            case 0:
            case 1:
                $this->lobby();
                break;
            case 2:
                $this->game();
                break;
            case 3:
                $this->restart();
                break;
        }
    }

    private function lobby() {
        if($this->getFillTeamsCount() >= intval($this->arenaData["teamsToStart"])) {
            if($this->phase == 0) {
                $this->progress["startTime"] = $startTime = intval($this->arenaData["startTime"]);
                foreach ($this->getAllPlayers() as $player) {
                    $player->sendMessage("§aGame starts in $startTime!");
                }
                $this->progress["startTime"]--;
                if($this->progress["startTime"] <= 0) {
                    $this->startGame();
                }
            }
            $this->phase = 1;
        }
        else {
            $this->phase = 0;
            foreach ($this->getAllPlayers() as $player) {
                $player->sendMessage(EggWars::getPrefix()." §7|| §cYou need more players!");
            }
        }
    }

    private function startGame() {

    }

    private function game() {

    }

    private function restart() {

    }

    public function getPlugin():EggWars {
        return EggWars::getInstance();
    }
}
