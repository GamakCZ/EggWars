<?php

declare(strict_types=1);

namespace eggwars\level;

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
        if(!Server::getInstance()->isLevelLoaded($data["levelName"])) {
            Server::getInstance()->loadLevel($data["levelName"]);
        }
        $this->level = Server::getInstance()->getLevelByName($data["levelName"]);
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
     * @return string $name
     *
     * (CustomName)
     */
    public function getName(): string {
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