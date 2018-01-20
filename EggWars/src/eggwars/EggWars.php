<?php

/*
 *    _____                __        __
 *   | ____|  __ _    __ _ \ \      / /__ _  _ __  ___
 *   |  _|   / _` | / _` |  \ \ /\ / // _` || '__|/ __|
 *   | |___ | (_| || (_| |   \ V  V /| (_| || |   \__ \
 *   |_____| \__, | \__, |    \_/\_/  \__,_||_|   |___/
 *           |___/  |___/
 */


declare(strict_types=1);

namespace eggwars;

use eggwars\commands\EggWarsCommand;
use eggwars\commands\TeamCommand;
use eggwars\commands\VoteCommand;
use eggwars\event\listener\ArenaSetupManager;
use eggwars\event\listener\LevelSetupManager;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;

/**
 * Class EggWars
 * @package eggwars
 */
class EggWars extends PluginBase {

    /** @var EggWars $instance */
    private static $instance;

    /** @var ArenaManager $arenaManager */
    private $arenaManager;

    /** @var LevelManager $levelManager */
    private $levelManager;

    /** @var ArenaSetupManager $arenaSetupManager */
    private $arenaSetupManager;

    /** @var LevelSetupManager $levelSetupManager */
    private $levelSetupManager;

    /** @var Command[] $commands */
    private $commands = [];

    public function onEnable() {
        self::$instance = $this;
        $this->registerCommands();
        $this->levelManager = new LevelManager;
        $this->arenaManager = new ArenaManager;
        $this->arenaSetupManager = new ArenaSetupManager;
        $this->levelSetupManager = new LevelSetupManager;
        $this->getLogger()->notice("You are running dev version of EggWars");
    }

    public function onDisable() {
        $this->getArenaManager()->saveArenas();
        $this->getLevelManager()->saveLevels();
    }

    private function registerCommands() {
        $this->commands["eggwars"] = new EggWarsCommand;
        $this->commands["vote"] = new VoteCommand;
        $this->commands["team"] = new TeamCommand;
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->register("eggwars", $command);
        }
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
