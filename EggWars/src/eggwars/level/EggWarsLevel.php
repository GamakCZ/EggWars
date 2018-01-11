<?php

declare(strict_types=1);

namespace eggwars\level;

use eggwars\EggWars;
use pocketmine\level\Level;
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
        /*if(!Server::getInstance()->isLevelLoaded($data["levelName"])) {
            Server::getInstance()->loadLevel($data["levelName"]);
        }*/
        if(Server::getInstance()->isLevelGenerated($data["levelName"])) {
            Server::getInstance()->loadLevel($data["levelName"]);
            $this->level = Server::getInstance()->getLevelByName($data["levelName"]);
        }
        else {
            EggWars::getInstance()->getLogger()->critical("§cCloud not load level {$data["levelName"]}!");
        }
        $this->data = $data;
        $this->teamsCount = count($data["teams"]);
    }

    /**
     * @param Config $config
     * @return EggWarsLevel
     */
    public static function loadFromConfig(Config $config) {
        return new EggWarsLevel($config->getAll());
    }

    /**
     * @return string $customName
     *
     * (CustomName)
     */
    public function getCustomName(): string {
        return $this->data["customName"];
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