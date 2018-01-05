<?php

declare(strict_types = 1);

namespace eggwars\arena;

use eggwars\EggWars;
use eggwars\LevelManager;
use eggwars\scheduler\EggWarsTask;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\tile\Sign;

/**
 * Class GeneratorScheduler
 * @package eggwars\arena
 */
class GeneratorScheduler extends EggWarsTask {

    const IRON = 2.2;
    const GOLD = 1.1;
    const DIAMOND = 0.4;

    /** @var  Arena $plugin */
    private $arena;

    /** @var null|\pocketmine\level\Level  */
    private $level;

    /**
     * GeneratorScheduler constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
        $this->level = $arena->getLevel();
    }

    public function onRun(int $currentTick) {
        if(!$this->level instanceof Level) return;
        if($this->getArena()->getPhase() == 2) {
            $this->spawn();
        }
    }

    private function spawn() {
        if(!$this->level instanceof Level) return;
        foreach ($this->level->getTiles() as $tile) {
            if($tile instanceof Sign) {
                if($tile->getText()[0] == "§3§lGenerator") {
                    $material = intval(str_replace("§2", "", $tile->getText()[1]));
                    $level = intval(str_replace("§7Level ", "", $tile->getText()[2]));
                    $this->getSpawnTime($material, $level);
                }
            }
        }
    }

    /**
     * @param int $material
     * @param int $level
     * @return float
     */
    private function getSpawnTime(int $material, int $level) {
        return floatval(10/$material*$level);
    }


    /**
     * @param Level $level
     */
    public function checkSigns(Level $level) {
        foreach ($level->getTiles() as $tile) {
            if($tile instanceof Sign) {
                $this->loadSign($tile);
            }
        }
    }

    /**
     * @param Sign $sign
     */
    private function loadSign(Sign $sign) {
        $text = $sign->getText();
        if($text[0] != "EggWars") return;

        $gen = $text[1];
        $lvl = $text[2];
        if(!in_array($gen, ["Gold", "Iron", "Diamond"])) return;
        if(!in_array($lvl, [0, 1, 2, 3, 4, 5])) return;
        $sign->setText("§3§lGenerator", "§2$gen", "§7Level $lvl", "§f§oclick to update");
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }

    public function getPlugin(): EggWars {
        return EggWars::getInstance();
    }
}