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

use eggwars\EggWars;
use eggwars\position\EggWarsVector;
use pocketmine\entity\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;

/**
 * Class EggWarsLevel
 * @package eggwars\level
 */
class EggWarsLevel {

    /**
     * @var Level $level
     */
    public $level;

    /**
     * @var array $data
     */
    public $data = [];

    /**
     * @var int $teamsCount
     */
    public $teamsCount;

    /**
     * EggWarsLevel constructor.
     * @param array $data
     */
    public function __construct(array $data) {
        if(Server::getInstance()->isLevelGenerated($data["folderName"])) {
            Server::getInstance()->loadLevel($data["folderName"]);
            $this->level = Server::getInstance()->getLevelByName($data["folderName"]);
            foreach ($this->level->getEntities() as $entity) {
                if($entity instanceof Item) {
                    $entity->close();
                }
            }
        }
        else {
            EggWars::getInstance()->getLogger()->critical("§cCloud not load level {$data["folderName"]}!");
        }
        $this->data = $data;
        $this->teamsCount = count($data["teams"]);
    }

    /**
     * @return bool
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
     * @param Config $config
     * @return EggWarsLevel
     */
    public static function loadFromConfig(Config $config) {
        return new EggWarsLevel($config->getAll());
    }

    /**
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
     * @param string $teamName
     * @return \pocketmine\math\Vector3|void
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
     * @param string $teamName
     * @param Vector3 $vector3
     */
    public function setSpawnVector(string $teamName, Vector3 $vector3) {
        if(empty($this->data["teams"][$teamName])) {
            $this->data["teams"][$teamName] = [];
        }
        $this->data["teams"][$teamName]["spawn"] = [$vector3->getX(), $vector3->getY(), $vector3->getZ()];
    }


    /**
     * @return string $customName
     *
     * (CustomName)
     */
    public function getCustomName(): string {
        return $this->data["name"];
    }

    /**
     * @return string $levelName
     *
     * (LevelName)
     */
    public function getLevelName(): string {
        return $this->data["levelName"];
    }

    /**
     * @return array $data
     */
    public function getLevelData(): array {
        return $this->data;
    }

    /**
     * @return int $teamsCount
     */
    public function getTeamsCount(): int {
        return $this->teamsCount;
    }

    /**
     * @return Level $level
     */
    public function getLevel(): Level {
        return $this->level;
    }
}