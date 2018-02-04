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

namespace eggwars\level;

use eggwars\arena\Arena;
use eggwars\EggWars;
use eggwars\position\EggWarsVector;
use pocketmine\entity\Creature;
use pocketmine\entity\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;

/**
 * Class EggWarsLevel
 * @package eggwars\level
 */
class EggWarsLevel {

    /** @var null|Level $level */
    public $level;

    /** @var array $data */
    public $data = [];

    /** @var int $teamsCount */
    public $teamsCount;

    /**
     * EggWarsLevel constructor.
     *
     * @param array $data
     */
    public function __construct(array $data) {

        $folderName = $data["folderName"];
        $teams = $data["teams"];

        if(!Server::getInstance()->isLevelGenerated($folderName)) {
            EggWars::getInstance()->getLogger()->critical("§cCloud not load level {$folderName}!");
            return;
        }

        if(!Server::getInstance()->isLevelLoaded($folderName)) {
            Server::getInstance()->loadLevel($folderName);
        }

        $this->data = $data;

        if(!$this->isValid()) {
            EggWars::getInstance()->getLogger()->critical("§cCloud not load level {$folderName} - level is not valid!");
            return;
        }

        $this->teamsCount = count($teams);


        /*$level = Server::getInstance()->getLevelByName($folderName);


        if(Server::getInstance()->isLevelGenerated($data["folderName"])) {
            Server::getInstance()->loadLevel($data["folderName"]);
            $this->level = Server::getInstance()->getLevelByName($data["folderName"]);
            foreach ($this->getLevel()->getEntities() as $entity) {
                if($entity instanceof Item) {
                    $entity->close();
                }
            }
            $this->getLevel()->setAutoSave(false);
        }
        else {
            EggWars::getInstance()->getLogger()->critical("§cCloud not load level {$data["folderName"]}!");
        }*/


    }


    /**
     * @return bool $valid
     */
    public function isValid(): bool {
        $valid = true;
        foreach ($this->data["teams"] as $team => ["spawn" => $spawnArgs, "egg" => $eggArgs]) {
            if(count($spawnArgs) !== 3 || count($eggArgs) !== 3) {
                $valid = false;
            }
        }
        return $valid;
    }


    /**
     * @api
     *
     * @param Arena $arena
     * @return Level $level
     */
    public function loadForGame(Arena $arena = null): Level {
        $folderName = $this->data["folderName"];

        if(!Server::getInstance()->isLevelLoaded($folderName)) {
            Server::getInstance()->loadLevel($folderName);
        }

        $level = Server::getInstance()->getLevelByName($folderName);

        $count = 0;
        foreach ($level->getEntities() as $entity) {
            if(!$entity instanceof Creature) {
                $entity->close();
                $count++;
            }
        }
        EggWars::getInstance()->getLogger()->alert("$count entities removed!");

        $level->setAutoSave(false);

        $this->level = $level;

        return $level;
    }

    /**
     * @api
     *
     * > Unload and save the level
     */
    public function unload() {
        $this->getLevel()->unload();
    }

    /**
     * @api
     *
     * @param string $teamName
     * @return \pocketmine\math\Vector3|null
     */
    public function getEggVector(string $teamName) {
        if(isset($this->data["teams"][$teamName])) {
            $vec =  EggWarsVector::fromArray($this->data["teams"][$teamName]["egg"])->asVector3();
            if($vec instanceof Vector3) {
                return $vec;
            }
            else {
                EggWars::getInstance()->getLogger()->critical("§cCloud not found egg for ($teamName)!");
            }
        }
        return null;
    }

    /**
     * @api
     *
     * @param string $teamName
     * @param Vector3 $vector3
     */
    public function setEggVector(string $teamName, Vector3 $vector3) {
        if(empty($this->data["teams"][$teamName])) {
            $this->data["teams"][$teamName] = [];
        }
        $this->data["teams"][$teamName]["egg"] = [$vector3->getX(), $vector3->getY(), $vector3->getZ()];
    }

    /**
     * @api
     *
     * @param string $teamName
     * @return \pocketmine\math\Vector3|null
     */
    public function getSpawnVector(string $teamName) {
        if(isset($this->data["teams"][$teamName])) {
            $vec =  EggWarsVector::fromArray($this->data["teams"][$teamName]["spawn"])->asVector3();
            if($vec instanceof Vector3) {
                return $vec;
            }
            else {
                EggWars::getInstance()->getLogger()->critical("§cCloud not found spawn for ($teamName)!");
            }
        }
        return null;
    }

    /**
     * @api
     *
     * @param string $teamName
     * @param Vector3 $vector3
     *
     * @return void
     */
    public function setSpawnVector(string $teamName, Vector3 $vector3) {
        if(empty($this->data["teams"][$teamName])) {
            $this->data["teams"][$teamName] = [];
        }
        $this->data["teams"][$teamName]["spawn"] = [$vector3->getX(), $vector3->getY(), $vector3->getZ()];
    }


    /**
     * @api
     *
     * @return string $customName
     *
     * (CustomName)
     */
    public function getCustomName(): string {
        return $this->data["name"];
    }

    /**
     * @api
     *
     * @return string $levelName
     *
     * (LevelName)
     */
    public function getLevelName(): string {
        return $this->data["levelName"];
    }

    /**
     * @api
     *
     * @return array $data
     */
    public function getLevelData(): array {
        return $this->data;
    }

    /**
     * @api
     *
     * @return int $teamsCount
     */
    public function getTeamsCount(): int {
        return $this->teamsCount;
    }

    /**
     * @api
     *
     * @return Level $level
     */
    public function getLevel(): Level {
        return $this->level;
    }
}