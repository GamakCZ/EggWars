<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\commands\EggWarsCommand;
use eggwars\commands\TeamCommand;
use eggwars\event\listener\ArenaSetupManager;
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
    private $setupManager;

    public function onEnable() {
        self::$instance = $this;
        $this->registerCommands();
        $this->levelManager = new LevelManager;
        $this->arenaManager = new ArenaManager;
        $this->setupManager = new ArenaSetupManager;
        $this->getLogger()->notice("You are running dev version of EggWars");
        $this->generateDefaultLevel();
        $this->loadTestArena();
    }

    private function generateDefaultLevel() {
        $this->getServer()->generateLevel("EggWars", 0, Generator::getGeneratorName(Flat::class));
    }

    private function loadTestArena() {
        $this->getArenaManager()->createArena("TestArena");
    }

    public function onDisable() {
        $this->arenaManager->saveArenas();
    }

    private function registerCommands() {
        $this->getServer()->getCommandMap()->register("eggwars", new EggWarsCommand);
        $this->getServer()->getCommandMap()->register("eggwars", new TeamCommand);
    }

    public function getSetupManager(): ArenaSetupManager {
        return $this->setupManager;
    }

    /**
     * @return ArenaManager $arenaManager
     */
    public function getArenaManager(): ArenaManager {
        return $this->arenaManager;
    }

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
