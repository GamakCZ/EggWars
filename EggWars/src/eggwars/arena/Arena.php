<?php

/*
 *    _____                  __        __
 *   | ____|   __ _    __ _  \ \      / /   __ _   _ __   ___
 *   |  _|    / _` |  / _` |  \ \ /\ / /   / _` | | '__| / __|
 *   | |___  | (_| | | (_| |   \ V  V /   | (_| | | |    \__ \
 *   |_____|  \__, |  \__, |    \_/\_/     \__,_| |_|    |___/
 *           |___/   |___/
 */

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\arena\listener\ArenaListener;
use eggwars\arena\scheduler\ArenaScheduler;
use eggwars\arena\scheduler\GeneratorScheduler;
use eggwars\arena\scheduler\RefreshSignScheduler;
use eggwars\arena\shop\ShopManager;
use eggwars\arena\team\Team;
use eggwars\arena\team\TeamManager;
use eggwars\arena\voting\VoteManager;
use eggwars\EggWars;
use eggwars\event\PlayerArenaJoinEvent;
use eggwars\event\PlayerArenaQuitEvent;
use eggwars\level\EggWarsLevel;
use eggwars\position\EggWarsPosition;
use eggwars\position\EggWarsVector;
use eggwars\utils\Color;
use pocketmine\block\Block;
use pocketmine\entity\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\Config;

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
    public $scheduler, $genScheduler;

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

    /** @var ShopManager $shopManager */
    public $shopManager;

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
        $this->shopManager = new ShopManager($this);

        // data
        $this->phase = 0;
        $this->progress["lobbyPlayers"] = [];

        // scheduler
        Server::getInstance()->getPluginManager()->registerEvents($this->listener = new ArenaListener($this), $this->getPlugin());
        Server::getInstance()->getScheduler()->scheduleRepeatingTask($this->scheduler = new ArenaScheduler($this), 20);
    }

    public function reloadGame() {
        $levels = $this->getPlugin()->getLevelManager()->getLevelsForArena($this);
        if(!$levels) {
            $this->arenaData["enabled"] = false;
            $this->getPlugin()->getLogger()->critical("Cloud not load levels for arena {$this->getName()}");
        }

        $this->voteManager->createVoteTable($levels);
        $this->teamManager->reloadTeams();

        $this->progress = [];
        $this->progress["lobbyPlayers"] = [];
        $this->phase = 0;
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
     * @api
     *
     * @return bool
     */
    public function isEnabled(): bool {
        return boolval($this->arenaData["enabled"]);
    }

    /**
     * @api
     *
     * @param bool|null $enabled
     */
    public function setEnabled(?bool $enabled) {
        if(is_bool($enabled)) {
            $this->arenaData["enabled"] = boolval($enabled);
        }
        else {
            $this->arenaData["enabled"] = true;
        }
    }

    /**
     * @api
     *
     * @return string $arenaName
     */
    public function getName(): string {
        return $this->arenaData["name"];
    }

    /**
     * @api
     *
     * @return int $phase
     */
    public function getPhase():int {
        return $this->phase;
    }

    /**
     * @api
     *
     * @param Player $player
     */
    public function addSpectator(Player $player) {
        $this->spectators[$player->getName()] = $player;
    }

    /**
     * @api
     *
     * @return Player[] $spectators
     */
    public function getSpectators(): array {
        return $this->spectators;
    }

    /**
     * @api
     *
     * @return Team[] $teams
     */
    public function getAllTeams() {
        if($this->teamManager instanceof TeamManager) {
            return $this->teamManager->teams;
        }
        return [];
    }

    /**
     * @api
     *
     * @param string $name
     * @return Team|null $team
     */
    public function getTeamByName(string $name):Team {
        return $this->teamManager->teams[$name];
    }

    /**
     * @api
     *
     * @param string $mc
     * @return Team|null $team
     */
    public function getTeamByMinecraftColor(string $mc) {
        /** @var Team|null $return */
        $return = null;
        foreach ($this->teamManager->teams as $name => $team) {
            if($team->getMinecraftColor() == $mc) {
                $return = $team;
            }
        }
        return $return;
    }

    /**
     * @api
     *
     * @param Player $player
     * @param string $teamName
     */
    public function addPlayerToTeam(Player $player, string $teamName) {

        /** @var Team $team */
        $team = $this->getTeamByName($teamName);

        /** @var Team $lastTeam */
        $lastTeam = $this->getTeamByPlayer($player);

        if($lastTeam instanceof Team) {
            unset($lastTeam->players[$player->getName()]);
        }

        if(!$team instanceof Team) {
            $player->sendMessage("§cTeam {$teamName} does not found!");
            return;
        }

        $player->sendMessage(EggWars::getPrefix()."§aYou are joined {$team->getDisplayName()}§a team!");
        $player->setNameTag($team->getMinecraftColor().$player->getName());
        $team->addPlayer($player);
    }

    /**
     * @api
     *
     * @param string $name
     * @return bool $teamExists
     */
    public function teamExists(string $name): bool {
        return isset($this->teamManager->teams[$name]);
    }

    /**
     * @api
     *
     * @param Vector3 $vector3
     * @return Team|null $team
     */
    public function getTeamEggByVector(Vector3 $vector3) {
        $team = null;
        foreach ($this->getMap()->data["teams"] as $teamName => $teamData) {
            $teamVec = EggWarsVector::fromArray($teamData["egg"]);
            if($teamVec->equals($vector3)) {
                $team = $this->getTeamByName($teamName);
            }
        }
        return $team;
    }

    /**
     * @api
     *
     * @param string $team
     * @return Vector3 $eggVector
     */
    public function getTeamEggVector(string $team): Vector3 {
        return $this->getMap()->getEggVector($team);
    }

    /**
     * @api
     *
     * @param string $team
     * @return Vector3 $spawn
     */
    public function getTeamSpawnVector(string $team): Vector3{
        return $this->getMap()->getSpawnVector($team);
    }

    /**
     * @api
     *
     * @param Player $player
     * @return Team|null $team
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
     * @api
     *
     * @return Level|null $level
     *
     * > Works only in 1 & 2 phase!
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
     * @api
     *
     * @return EggWarsLevel $level
     */
    public function getMap() {
        return $this->level;
    }

    /**
     * @api
     *
     * @param string $message
     *
     * > without spectators
     */
    public function broadcastMessage(string $message) {
        foreach ($this->getAllPlayers() as $player) {
            $player->sendMessage($message);
        }
    }

    /**
     * @api
     *
     * @param Player $player
     * @param string $team
     */
    public function joinPlayer(Player $player, $team = null) {
        $event = new PlayerArenaJoinEvent($player, $this);
        $this->getPlugin()->getServer()->getPluginManager()->callEvent($event);

        if(!boolval($this->arenaData["enabled"])) {
            return;
        }

        if(empty($this->progress["lobbyPlayers"]) || !is_array($this->progress["lobbyPlayers"])) {
            $this->progress["lobbyPlayers"] = [];
        }

        $this->progress["lobbyPlayers"][$player->getName()] = $player;

        $player->teleport(EggWarsPosition::fromArray($this->arenaData["lobby"], $this->arenaData["lobby"][3]));
        $player->setGamemode($player::ADVENTURE);
        $player->setMaxHealth(20);
        $player->setHealth(20);
        $player->setFood(20);
        $player->setScale(1.0);
        $player->setAllowFlight(false);
        $player->setXpProgress(0);
        $player->getInventory()->clearAll();
        $player->removeAllEffects();

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
     * @api
     *
     * @param Player $player
     * @param int $message
     */
    public function disconnectPlayer(Player $player, $message = 0) {
        $this->getPlugin()->getServer()->getPluginManager()->callEvent(new PlayerArenaQuitEvent($player, $this));
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
        $player->setNameTag($player->getName());
        switch ($message) {
            case 0:
                $player->sendMessage(EggWars::getPrefix()."§aYou are successfully leaved arena!");
                break;
            case 1:
                break;
        }

    }

    /**
     * @api
     *
     * @return int
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
     * @api
     *
     * @return Player[] $players
     *
     * > without spectators
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
     * 1) game - game
     * 2) restart - restarting
     *
     */

    public function progress() {
        switch ($this->phase) {
            case 0: $this->lobby(); break;
            case 1: $this->game(); break;
            case 2: $this->restart(); break;
        }
    }

    /**
     * Last update:
     *
     * @version EggWars 1.0.0
     */
    public function startGame() {

        // Add players to teams:
        foreach ($this->getAllPlayers() as $player) {
            if ($this->getTeamByPlayer($player) == null) {
                choose:
                $team = $this->getAllTeams()[array_rand($this->getAllTeams(), 1)];
                if (!$team->isFull()) {
                    $team->addPlayer($player);
                    $player->sendMessage(EggWars::getPrefix() . "§7You are joined " . $team->getMinecraftColor() . $team->getTeamName() . "§7 team!");
                } else {
                    goto choose;
                }

            }
        }

        foreach ($this->getAllPlayers() as $player) {
            $player->setGamemode($player::SURVIVAL);
            $player->setFood(20);
            $player->setHealth(20);
            $player->setMaxHealth(20);
            $player->getInventory()->clearAll();
            $player->addTitle("§6Game started!", "map created by: ".$this->arenaData["builder"]);
            $player->teleport(Position::fromObject($this->getTeamSpawnVector($this->getTeamByPlayer($player)->getTeamName()), $this->getLevel()));
        }

        $this->phase = 1;

        // Task data
        $this->progress["gameTime"] = $this->arenaData["gameTime"];
        $this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this->genScheduler = new GeneratorScheduler($this), 1);
    }

    /**
     * Last update:
     *
     * @version EggWars 1.0.0
     */
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

                switch ($startTime) {
                    case 5: $player->addTitle("§c5"); break;
                    case 4: $player->addTitle("§64"); break;
                    case 3: $player->addTitle("§e3"); break;
                    case 2: $player->addTitle("§a2"); break;
                    case 1: $player->addTitle("§21"); break;
                }
            }

            if($startTime == 5) {
                $map = $this->voteManager->getMap();
                $map->loadForGame($this);
                $this->level = $map;
                $this->broadcastMessage(EggWars::getPrefix()."§e{$map->getCustomName()} §6chosen.");
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

    /**
     * Last update:
     *
     * @version EggWars 1.0.0
     */
    public function game() {
        // END
        if($this->teamManager->checkEnd()) {
            $lastTeam = $this->teamManager->getLastTeam();
            foreach ($lastTeam->getTeamsPlayers() as $player) {
                $player->addTitle("§aCONGRATULATION!", "§fYou are won the game");
                $player->sendMessage("§aYou are won the game!");
            }
            $this->phase = 2;
        }
        if($this->progress["gameTime"] <= 0) {
            foreach ($this->getAllPlayers() as $player) {
                $player->addTitle("§cGAME OVER!", "§6Time is up!");
                $this->phase = 2;
            }
        }
        $this->progress["gameTime"]--;
        // PROGRESS BAR
        foreach ($this->getAllPlayers() as $player) {
            $player->sendTip($this->teamManager->getBarFormat());
        }
    }

    private function restart() {
        if(empty($this->progress["restartTime"])) {
            $this->progress["restartTime"] = $this->arenaData["restartTime"];
        }
        $this->progress["restartTime"]--;
        /** @var Player[] $players */
        $players = array_merge($this->getAllPlayers(), $this->getSpectators());
        foreach ($players as $player) {
            $player->sendTip(EggWars::getPrefix()."§aRestarting in {$this->progress["restartTime"]}");
        }
        if($this->progress["restartTime"] == 0) {
            foreach ($players as $player) {
                $this->disconnectPlayer($player, 1);
            }
            $this->getMap()->unload();
            $this->reloadGame();
        }
    }

    public function getPlugin():EggWars {
        return EggWars::getInstance();
    }
}
