<?php

declare(strict_types=1);

namespace vixikhd\eggwars\arena\level;

use pocketmine\level\Level;
use vixikhd\eggwars\arena\Arena;

/**
 * Class DefaultManager
 * @package vixikhd\eggwars\arena\level
 */
class BaseLevelManager implements LevelManager {

    /** @var Arena $plugin */
    public $plugin;

    /** @var Level $level */
    public $level;

    /** @var array $levelData */
    public $levelData;

    /** @var bool $isMapSaved */
    private $isMapSaved = false;

    public function init(Arena $plugin): bool {
        $this->plugin = $plugin;
        $this->levelData = $this->plugin->plugin->levels[$this->plugin->data["levels"][0]];
        $server = $this->plugin->plugin->getServer();
        if(!$server->isLevelGenerated($this->levelData["level"])) {
            
        }
    }

    /**
     * @return Level
     */
    public function getLevel(): Level {
        return $this->level;
    }

    /**
     * @return void
     */
    public function chooseMap() {
        if(!$this->isMapSaved) {
            $this->plugin->mapReset->saveMap($this->level);
            $this->isMapSaved = true;
        }
    }

    /**
     * @return array
     */
    public function getLevelData(): array {
        return $this->levelData;
    }
}