<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\arena\listener\ArenaListener;
use eggwars\arena\scheduler\ArenaScheduler;
use eggwars\arena\scheduler\GeneratorScheduler;
use eggwars\arena\scheduler\RefreshSignScheduler;
use eggwars\arena\team\Team;
use eggwars\arena\team\TeamManager;
use eggwars\arena\voting\VoteManager;
use eggwars\EggWars;
use eggwars\level\EggWarsLevel;
use eggwars\position\EggWarsPosition;
use eggwars\position\EggWarsVector;
use eggwars\utils\Color;
use pocketmine\block\Block;
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

    /** @var array $arenaData */
    public $arenaData = [];

    /** @var array $progress */
    public $progress = [];

    /**
     * @var int $time
     * @var int $phase
     */
    public $time, $phase = 0;

    /**
     * @var Task $scheduler
     * @var Task $genScheduler
     */
    private $scheduler, $genScheduler;

    /** @var ArenaListener $listener */
    private $listener;

    /** @var Player[] $spectators */
    private $spectators = [];

    /** @var EggWarsLevel $level */
    private $level = null;

    /** @var VoteManager $voteManager */
    public $voteManager;

    /** @var TeamManager $teamManager */
    public $teamManager;

    /**
     * Arena constructor.
     * @param EggWars $eggWars
     * @param Config $config
     */
    public function __construct(EggWars $eggWars, Config $config) {
        $this->arenaData = $config->getAll();
        $this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask(new RefreshSignScheduler($this), 20*5);
        if(boolval($this->arenaData["enabled"])) {
            $this->loadGame();
        }
    }

    public function loadGame() {
        // loading levels
        $levels = $this->getPlugin()->getLevelManager()->getLevelsForArena($this);
        if(!$levels) {
            $this->arenaData["enabled"] = false;
            $this->getPlugin()->getLogger()->critical("Cloud not load levels for arena {$this->getName()}");
            return;
        }
        $this->loadLevel();

        // managers
        $this->voteManager = new VoteManager($this, $levels);
        $this->teamManager = new TeamManager($this);

        // data
        $this->phase = 0;
        $this->progress["lobbyPlayers"] = [];

        // scheduler
        Server::getInstance()->getPluginManager()->registerEvents($this->listener = new ArenaListener($this), $this->getPlugin());
        Server::getInstance()->getScheduler()->scheduleRepeatingTask($this->scheduler = new ArenaScheduler($this), 20);
    }

    private function loadLevel() {
        if(!Server::getInstance()->isLevelGenerated($this->arenaData["lobby"][3])) {
            $this->getPlugin()->getLogger()->critical("Arena level not found!");
            $this->arenaData["enabled"] = false;
        }
        else {
            if(!Server::getInstance()->isLevelLoaded($this->arenaData["lobby"][3])) {
                Server::getInstance()->loadLevel($this->arenaData["lobby"][3]);
            }
        }
    }



    /**
     * @return bool
     */
    public function isEnabled(): bool {
        return boolval($this->arenaData["enabled"]);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->arenaData["name"];
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
        if($this->teamManager instanceof TeamManager) {
            return $this->teamManager->teams;
        }
        return [];
    }

    /**
     * @param string $name
     * @return Team
     */
    public function getTeamByName(string $name):Team {
        return $this->teamManager->teams[$name];
    }

    /**
     * @param string $mc
     * @return Team|null
     */
    public function getTeamByMinecraftColor(string $mc) {
        $rteam = null;
        foreach ($this->teamManager->teams as $name => $team) {
            if($team->getMinecraftColor() == $mc) {
                $rteam = $team;
            }
        }
        return $rteam;
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
        return isset($this->teamManager->teams[$name]);
    }

    /**
     * @param Vector3 $vector3
     * @return Team|null $team
     */
    public function getTeamEggByVector(Vector3 $vector3) {
        $team = null;
        foreach ($this->level->data["teams"] as $teamName => $teamData) {
            $teamVec = EggWarsVector::fromArray($teamData["egg"]);
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
        return $this->level->getEggVector($team);
    }

    /**
     * @param string $team
     * @return Vector3 $spawn
     */
    public function getTeamSpawnVector(string $team): Vector3{
        return $this->level->getSpawnVector($team);
    }

    /**
     * @param Player $player
     * @return Team
     */
    public function getTeamByPlayer(Player $player) {
        $team = null;
        foreach ($this->getAllTeams() as $teams) {
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
        if($this->level != null) {
            return $this->level->getLevel();
        }
        if(Server::getInstance()->isLevelGenerated($this->arenaData["level"])) {
            return Server::getInstance()->getLevelByName($this->arenaData["level"]);
        }
    }

    /**
     * @return EggWarsLevel $level
     */
    public function getMap() {
        return $this->level;
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
        if(!boolval($this->arenaData["enabled"])) {
            return;
        }
        if(empty($this->progress["lobbyPlayers"]) || !is_array($this->progress["lobbyPlayers"])) {
            $this->progress["lobbyPlayers"] = [];
        }

        $this->progress["lobbyPlayers"][$player->getName()] = $player;

        $player->teleport(EggWarsPosition::fromArray($this->arenaData["lobby"], $this->arenaData["lobby"][3]));
        $player->setGamemode($player::ADVENTURE);
        $player->setHealth(20);
        $player->setFood(20);
        $player->setAllowFlight(false);
        $player->setXpProgress(0);
        $player->getInventory()->clearAll();

        $t = 0;
        foreach ($this->getAllTeams() as $team) {
            $player->getInventory()->setItem($t, Color::getWoolFormMC($team->getMinecraftColor())->setCustomName("§7Join ".$team->getMinecraftColor().$team->getTeamName()));
            $t++;
        }

        $count = count($this->getAllPlayers());
        $maxCount = count($this->getAllTeams())*intval($this->arenaData["playersPerTeam"]);

        $player->sendMessage(EggWars::getPrefix()."§aYou are joined the game!");
        $this->broadcastMessage(EggWars::getPrefix()."§a{$player->getName()} joined EggWars game §7[$count/$maxCount]!");

        if($team != null) {
            if($this->teamExists(strval($team->getTeamName()))) {
                $this->addPlayerToTeam($player, strval($team->getTeamName()));
            }
            else {
                $this->getPlugin()->getLogger()->critical("Team {$team->getTeamName()} was not found.");
            }
        }
    }

    /**
     * @param Player $player
     */
    public function disconnectPlayer(Player $player) {
        if($this->getTeamByPlayer($player) instanceof Team) {
            unset($this->getTeamByPlayer($player)->players[$player->getName()]);
        }
        if(isset($this->progress["lobbyPlayers"][$player->getName()])) {
            unset($this->progress["lobbyPlayers"][$player->getName()]);
        }
        if(isset($this->progress["spectators"][$player->getName()])) {
            unset($this->progress["spectators"][$player->getName()]);
        }
        $player->setGamemode($this->getPlugin()->getServer()->getDefaultGamemode());
        $player->setHealth(20);
        $player->setFood(20);
        $player->getInventory()->clearAll();
        $player->teleport($this->getPlugin()->getServer()->getDefaultLevel()->getSpawnLocation()->asPosition());
        $player->sendMessage(EggWars::getPrefix()."§aYou are successfully leaved arena!");
    }

    /**
     * @return int $int
     */
    public function getFillTeamsCount():int {
        $count = 0;
        foreach ($this->getAllTeams() as $team) {
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
            if(isset($this->progress["lobbyPlayers"])) {
                /** @var Player $player */
                foreach ($this->progress["lobbyPlayers"] as $player) {
                    if(empty($returnPlayers[$player->getName()])) {
                        $returnPlayers[$player->getName()] = $player;
                    }
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
                $this->lobby();
                break;
            case 1:
                $this->game();
                break;
            case 2:
                $this->restart();
                break;
        }
    }

    public function lobby() {

        foreach ($this->getAllPlayers() as $player) {
            $player->sendTip($this->voteManager->getBarFormat());
        }

        // if game is ready to start
        if($this->getFillTeamsCount() >= intval($this->arenaData["teamsToStart"])) {
            // check for start
            if(empty($this->progress["startTime"]) || !is_numeric($this->progress["startTime"])) {
                $this->progress["startTime"] = $startTime = intval($this->arenaData["startTime"]);
                foreach ($this->getAllPlayers() as $player) {
                    $player->sendMessage(EggWars::getPrefix()."§7Game starts in $startTime sec!");
                }
            }
            $this->progress["startTime"]--;
            $startTime = $this->progress["startTime"];


            // start
            if(intval($this->progress["startTime"]) <= 0) {
                $this->startGame();
            }

            foreach ($this->getAllPlayers() as $player) {
                $player->sendPopup(EggWars::getPrefix()." §7|| §aGame starts in {$this->progress["startTime"]}");
            }

            if($startTime == 5) {
                $map = $this->voteManager->getMap();
                $this->level = $map;
                $this->broadcastMessage(EggWars::getPrefix()."§e{$map->getCustomName()} §6chosen.");
            }

            if($startTime == 5) {
                $player->addTitle("", "§c5");
            }

            if($startTime == 4) {
                $player->addTitle("", "§64");
            }

            if($startTime == 3) {
                $player->addTitle("", "§e3");
            }

            if($startTime == 2) {
                $player->addTitle("", "§a2");
            }

            if($startTime == 1) {
                $player->addTitle("", "§21");
            }

            if(in_array($startTime, [120, 90, 60, 45, 30, 20 ,15, 10])) {
                $this->broadcastMessage(EggWars::getPrefix()."§7Game starts in {$startTime} sec!");
            }
        }
        else {
            foreach ($this->getAllPlayers() as $player) {
                $player->sendPopup(EggWars::getPrefix()." §7|| §cYou need more players!");
            }
        }
    }

    public function startGame() {
        // ADD PLAYERS TO TEAMS
        foreach ($this->getAllPlayers() as $player) {
            if($this->getTeamByPlayer($player) == null) {
                foreach ($this->getAllTeams() as $team) {
                    choose:
                    if(!$team->isFull()) {
                        $team->addPlayer($player);
                        $player->sendMessage(EggWars::getPrefix()."§7You are joined ".$team->getMinecraftColor().$team->getTeamName()."§7 team!");
                    }
                    else {
                        goto choose;
                    }
                }
            }
        }

        foreach ($this->getAllTeams() as $team) {
            $this->getLevel()->setBlock($this->getTeamEggVector($team->getTeamName()), Block::get(Block::DRAGON_EGG));
        }

        // data
        foreach ($this->getAllPlayers() as $player) {
            $player->setGamemode($player::SURVIVAL);
            $player->setFood(20);
            $player->setHealth(20);
            $player->setMaxHealth(20);
            $player->getInventory()->clearAll();
            $player->addTitle("§6Game started!", "map builded by: ".$this->arenaData["builder"]);
            $player->teleport(Position::fromObject($this->getTeamSpawnVector($this->getTeamByPlayer($player)->getTeamName()), $this->getLevel()));
        }
        $this->phase = 1;

        // gametime
        $this->progress["gameTime"] = $this->arenaData["gameTime"];
        $this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this->genScheduler = new GeneratorScheduler($this), 1);
    }

    private function game() {
        // END
        if($this->teamManager->checkEnd()) {
            $lastTeam = $this->teamManager->getLastTeam();
            foreach ($lastTeam->getTeamsPlayers() as $player) {
                $player->addTitle("§aCONGRATULATION!", "§fYou are won the game");
                $player->sendMessage("§aYou are won the game!");
            }
            $this->phase = 2;
        }
        $this->progress["gameTime"]--;
        // PROGRESS BAR
        foreach ($this->getAllPlayers() as $player) {
            $player->sendTip($this->teamManager->getBarFormat());
        }
    }

    private function restart() {
        if(isset($this->progress["restartTime"])) {
            $this->progress["restartTime"] = $this->arenaData["restartTime"];
        }
        $this->progress["restartTime"]--;
        /** @var Player[] $players */
        $players = array_merge($this->getAllPlayers(), $this->getSpectators());
        foreach ($players as $player) {
            $player->sendTip("§aRestarting in {$this->progress["restartTime"]}");
        }
        if($this->progress["restartTime"] == 0) {
            $this->getLevel()->unload();
            $this->loadGame();
        }
    }

    public function getPlugin():EggWars {
        return EggWars::getInstance();
    }
}
