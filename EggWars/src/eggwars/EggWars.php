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

namespace eggwars;

use eggwars\commands\EggWarsCommand;
use eggwars\commands\TeamCommand;
use eggwars\commands\VoteCommand;
use eggwars\event\listener\ArenaSetupManager;
use eggwars\event\listener\LevelSetupManager;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\Generator;
use pocketmine\plugin\PluginBase;

/**
 * Class EggWars
 * @package eggwars
 */
class EggWars extends PluginBase {

    /**
     * @var EggWars $instance
     */
    private static $instance;

    /**
     * @var ArenaManager $arenaManager
     */
    private $arenaManager;

    /**
     * @var LevelManager $levelManager
     */
    private $levelManager;

    /**
     * @var ArenaSetupManager $setupManager
     */
    private $arenaSetupManager;

    /**
     * @var LevelSetupManager $levelSetupManager
     */
    private $levelSetupManager;

    public function onEnable() {
        self::$instance = $this;
        $this->registerCommands();
        $this->levelManager = new LevelManager;
        $this->arenaManager = new ArenaManager;
        $this->arenaSetupManager = new ArenaSetupManager;
        $this->levelSetupManager = new LevelSetupManager;
        $this->getLogger()->notice("You are running dev version of EggWars");
        #$this->generateDefaultLevel();
        #$this->loadTestArena();
    }

    private function generateDefaultLevel() {
        $this->getServer()->generateLevel("EggWars", 0, Generator::getGeneratorName(Flat::class));
    }

    private function loadTestArena() {
        $this->getArenaManager()->createArena("TestArena");
    }

    public function onDisable() {
        $this->getArenaManager()->saveArenas();
        $this->getLevelManager()->saveLevels();
    }

    private function registerCommands() {
        $this->getServer()->getCommandMap()->register("eggwars", new EggWarsCommand);
        $this->getServer()->getCommandMap()->register("eggwars", new TeamCommand);
        $this->getServer()->getCommandMap()->register("eggwars", new VoteCommand);
    }

    /**
     * @return ArenaSetupManager $arenaSetupManager
     */
    public function getSetupManager(): ArenaSetupManager {
        return $this->arenaSetupManager;
    }

    /**
     * @return LevelSetupManager $levelSetupManager
     */
    public function getLevelSetupManager(): LevelSetupManager {
        return $this->levelSetupManager;
    }

    /**
     * @return ArenaManager $arenaManager
     */
    public function getArenaManager(): ArenaManager {
        return $this->arenaManager;
    }

    /**
     * @return LevelManager $levelManager
     */
    public function getLevelManager(): LevelManager {
        return $this->levelManager;
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix(): string {
        return "§3[EggWars] ";
    }

    /**
     * @return EggWars $plugin
     */
    public static function getInstance(): EggWars {
        return self::$instance;
    }
}
