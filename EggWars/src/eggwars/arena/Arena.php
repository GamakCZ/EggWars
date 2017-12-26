<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
use eggwars\level\EggWarsLevel;
use eggwars\position\EggWarsPosition;
use eggwars\position\EggWarsVector;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
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
     * @var Task $genScheduler
     */
    private $scheduler, $genScheduler;

    /**
     * @var Listener $listener
     */
    private $listener;

    /**
     * @var Player[] $spectators
     */
    private $spectators = [];

    /**
     * @var EggWarsLevel $level
     */
    private $level = null;

    /**
     * @var VoteManager $voteManager
     */
    private $voteManager;

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
        $this->progress["lobbyPlayers"] = [];
        Server::getInstance()->getPluginManager()->registerEvents($this->listener = new ArenaListener($this), $this->getPlugin());
        Server::getInstance()->getScheduler()->scheduleRepeatingTask($this->scheduler = new ArenaScheduler($this), 20);
        $this->voteManager = new VoteManager($this, $this->getPlugin()->getLevelManager()->getLevelsForArena($this->arenaData["teamsCount"]));
        #Server::getInstance()->getScheduler()->scheduleRepeatingTask($this->genScheduler = new GeneratorScheduler($this), 1);
    }

    private function loadLevel() {
        if(!Server::getInstance()->isLevelGenerated($this->arenaData["lobby"][3])) {
            $this->getPlugin()->getLogger()->critical("Arena level not found!");
            $this->getPlugin()->getPluginLoader()->disablePlugin($this->getPlugin());
        }
        else {
            Server::getInstance()->loadLevel($this->arenaData["lobby"][3]);
        }
    }

    private function loadTeams() {
        foreach ($this->arenaData["teams"] as $team => $data) {
            $color = strval($data["color"]);
            $this->teams[$team] = new Team($this, $team, $color, []);
        }
    }

    /**
     * @return int $phase
     */
    public function getPhase():int {
        return $this->phase;
    }

    /**
     * @param Player $player
     */
    public function addSpectator(Player $player) {
        $this->spectators[$player->getName()] = $player;
    }

    /**
     * @return Player[] $spectators
     */
    public function getSpectators(): array {
        return $this->spectators;
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
        //array_push($this->getTeamByName($teamName)->players, $player);
        $team = $this->getTeamByName($teamName);
        $lastTeam = $this->getTeamByPlayer($player);
        if($lastTeam instanceof Team) {
            unset($lastTeam->players[$player->getName()]);
        }
        $team->addPlayer($player);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function teamExists(string $name): bool {
        return isset($this->teams[$name]);
    }

    /**
     * @param Vector3 $vector3
     * @return Team|null $team
     */
    public function getTeamEggByVector(Vector3 $vector3) {
        $team = null;
        foreach ($this->arenaData["teams"] as $teamName => $teamData) {
            $teamVec = EggWarsVector::__fromArray($teamData["egg"]);
            if($teamVec->equals($vector3)) {
                $team = $this->getTeamByName($teamName);
            }
        }
        return $team;
    }

    /**
     * @param string $team
     * @return Vector3 $egg
     */
    public function getTeamEggVector(string $team): Vector3 {
        return EggWarsVector::__fromArray($this->arenaData["teams"][$team]["egg"])->asVector3();
    }

    /**
     * @param string $team
     * @return Vector3 $spawn
     */
    public function getTeamSpawnVector(string $team): Vector3{
        return EggWarsVector::__fromArray($this->arenaData["teams"][$team]["spawn"])->asVector3();
    }

    /**
     * @param Player $player
     * @return Team
     */
    public function getTeamByPlayer(Player $player) {
        $team = null;
        foreach ($this->teams as $teams) {
            foreach ($teams->getTeamsPlayers() as $players) {
                if($player->getName() == $players->getName()) {
                    $team = $teams;
                }
            }
        }
        return $team;
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
     * @param string $message
     */
    public function broadcastMessage(string $message) {
        foreach ($this->getAllPlayers() as $player) {
            $player->sendMessage($message);
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

        $this->progress["lobbyPlayers"][$player->getName()] = $player;

        $player->teleport(EggWarsPosition::__fromArray($this->arenaData["lobby"], $this->arenaData["level"]));
        $player->setGamemode($player::ADVENTURE);
        $player->setHealth(20);
        $player->setFood(20);
        $player->setAllowFlight(false);
        $player->setXpProgress(0);
        $player->getInventory()->clearAll();

        $count = count($this->getAllPlayers());
        $maxCount = count($this->getAllTeams())*intval($this->arenaData["playersPerTeam"]);

        $player->sendMessage(EggWars::getPrefix()."§aYou are joined the game!");
        $this->broadcastMessage(EggWars::getPrefix()."§a{$player->getName()} joined EggWars game §7[$count/$maxCount]!");

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
    public function getAllPlayers(): array {
        /** @var Player[] $returnPlayers */
        $returnPlayers = [];
        if($this->getPhase() == 0 || $this->getPhase() == 1) {
            foreach ($this->getAllTeams() as $team) {
                foreach ($team->getTeamsPlayers() as $player) {
                    if(empty($returnPlayers[$player->getName()])) {
                        $returnPlayers[$player->getName()] = $player;
                    }
                }
            }
            /** @var Player $player */
            foreach ($this->progress["lobbyPlayers"] as $player) {
                if(empty($returnPlayers[$player->getName()])) {
                    $returnPlayers[$player->getName()] = $player;
                }
            }
        }
        else {
            foreach ($this->getAllTeams() as $team) {
                $returnPlayers = array_merge($returnPlayers, $team->getTeamsPlayers());
            }
        }
        return $returnPlayers;
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
     * @param int $time
     * @return string
     */
    public function calculateTime(int $time): string {
        $min = (int)$time/60;
        if(!is_int($min)) {
            $min = intval($min);
        }
        $min = strval($min);
        if(strlen($min) == 0) {
            $min = "00";
        }
        elseif(strlen($min) == 1) {
            $min = "0{$min}";
        }
        else {
            $min = strval($min);
        }
        $sec = $time%60;
        if(!is_int($sec)) {
            $sec = intval($sec);
        }
        $sec = strval($sec);
        if(strlen($sec) == 0) {
            $sec = "00";
        }
        elseif(strlen($sec) == 1) {
            $sec = "0{$sec}";
        }
        else {
            $sec = strval($sec);
        }
        if($time <= 0) {
            return "00:00";
        }
        return strval($min.":".$sec);
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
            // first check
            if($this->phase == 0) {
                $this->progress["startTime"] = $startTime = intval($this->arenaData["startTime"]);
                foreach ($this->getAllPlayers() as $player) {
                    $player->sendMessage(EggWars::getPrefix()."§7Game starts in $startTime sec!");
                }

            }
            $this->progress["startTime"]--;
            if($this->progress["startTime"] <= 0) {
                $this->startGame();
                $this->phase = 2;
            }
            else {
                $this->phase = 1;
            }
            foreach ($this->getAllPlayers() as $player) {
                $player->sendTip(EggWars::getPrefix()." §7|| §aGame starts in {$this->progress["startTime"]}");
            }
            switch ($this->progress["startTime"]) {
                case 120:
                case 90:
                case 60:
                case 45:
                case 30:
                case 20:
                case 15:
                case 10:
                case 5:
                case 3:
                case 2:
                case 1:
                    $this->broadcastMessage(EggWars::getPrefix()."§7Game starts in  {$this->progress["startTime"]} sec!");
                    break;
            }
        }
        else {
            $this->phase = 0;
            foreach ($this->getAllPlayers() as $player) {
                $player->sendTip(EggWars::getPrefix()." §7|| §cYou need more players!");
            }
        }
    }

    private function startGame() {
        // ADD PLAYERS TO TEAMS
        foreach ($this->getAllPlayers() as $player) {
            if($this->getTeamByPlayer($player) == null) {
                foreach ($this->getAllTeams() as $team) {
                    if(!$team->isFull()) {
                        $team->addPlayer($player);
                        $player->sendMessage(EggWars::getPrefix()."§7You are joined ".$team->getColor().$team->getTeamName()."§7 team!");
                    }
                }
            }
        }

        foreach ($this->getAllTeams() as $team) {
            $this->getLevel()->setBlock($this->getTeamEggVector($team->getTeamName()), Block::get(Block::DRAGON_EGG));
        }

        // DATA
        foreach ($this->getAllPlayers() as $player) {
            $player->setGamemode($player::SURVIVAL);
            $player->setFood(20);
            $player->setHealth(20);
            $player->setMaxHealth(20);
            $player->getInventory()->clearAll();
            $player->addTitle("§6Game started!", "map builded by: ".$this->arenaData["builder"]);
            $player->teleport($vec = $this->getTeamSpawnVector($this->getTeamByPlayer($player)->getTeamName()));
            $player->setSpawn(Position::fromObject($vec, $this->getLevel()));
        }
        $this->phase = 2;

        // GAMETIME
        $this->progress["gameTime"] = $this->arenaData["gameTime"];
    }

    private function game() {

        // CHECK END
        if($this->getFillTeamsCount() <= 1) {
            /** @var Team|null $lastTeam */
            $lastTeam = null;
            while ($lastTeam == null) {
                foreach ($this->getAllPlayers() as $player) {
                    $lastTeam = $this->getTeamByPlayer($player);
                }
            }

            $players = array_merge($this->getAllPlayers(), $this->spectators);
            /** @var Player $player */
            foreach ($players as $player) {
                $player->addTitle("§aTeam ".$lastTeam->getColor().$lastTeam->getTeamName()." won the game!");
            }
        }

        // PROGRESS BAR
        $m = "\n".str_repeat(" ", 50);
        $t = $m;
        foreach ($this->teams as $teams) {
            $t = $teams->getColor().$teams->getTeamName().$teams->isAlive() ? "§a✔" : "§4✖".$m;
        }
        $format = $m."§3EggWars §7|| §6".$this->calculateTime($this->arenaData["gameTime"]-$this->progress["gameTime"]).$m.$t;
        foreach ($this->getAllPlayers() as $player) {
            $player->sendTip($format);
        }
    }

    private function restart() {

    }

    public function getPlugin():EggWars {
        return EggWars::getInstance();
    }
}
