<?php

declare(strict_types=1);

namespace eggwars;

use eggwars\arena\Arena;
use eggwars\commands\EggWarsCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class EggWars extends PluginBase{

    /** @var  EggWars $instance */
    private static $instance;

    /** @var Arena[] $arenas */
    private $arenas = [];

    public function onEnable() {
        self::$instance = $this;
        $this->loadArenas();
        $this->registerCommands();
    }

    private function registerCommands() {
        $this->getServer()->getCommandMap()->register("eggwars", new EggWarsCommand);
    }

    /**
     * @return EggWars
     */
    public static function getInstance(): EggWars {
        return self::$instance;
    }


    /**
     * @param Player $player
     * @return Arena $arena
     */
    public function getArenaByPlayer(Player $player):Arena {
        $return = null;
        foreach ($this->arenas as $arena) {
            if($arena->inGame($player)) {
                $return = $arena;
            }
        }
        return $return;
    }

    public function loadArenas() {
        $count = 0;
        $time = microtime(true);
        foreach (glob($this->getDataFolder()."arenas/*.yml") as $file) {
            $fileName = basename($file);
            $this->getLogger()->info("§6Loading {$fileName} arena...");
            $config = new Config($file, Config::YAML);
            if(!$this->getServer()->isLevelLoaded(strval($config->get("level")))) {
                $this->getServer()->loadLevel(strval($config->get("level")));
            }
            $this->arenas[$fileName] = new Arena($this, $config);
            $this->getLogger()->info("§aArena {$fileName} loaded!");
            $count++;
        }
        $tme = microtime(true)-$time;
        $this->getLogger()->info("§a{$count} arenas loaded, {$tme} sec.");
    }
}
