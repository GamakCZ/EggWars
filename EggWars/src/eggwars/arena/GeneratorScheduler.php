<?php

declare(strict_types = 1);

namespace eggwars\arena;

use eggwars\EggWars;
use eggwars\scheduler\EggWarsTask;
use pocketmine\block\Block;
use pocketmine\tile\Sign;

/**
 * Class GeneratorScheduler
 * @package eggwars\arena
 */
class GeneratorScheduler extends EggWarsTask {

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
        $this->checkSigns();
    }

    public function onRun(int $currentTick) {
        if($this->getArena()->getPhase() == 2) {
            $this->spawn();
        }
    }

    private function spawn() {
        $level = $this->level;
        foreach ($level->getTiles() as $tile) {
            switch ($level->getBlock($tile->add(0, -1, 0))->getId()) {
                case Block::IRON_BLOCK:
                    break;
                case Block::GOLD_BLOCK:
                    break;
                case Block::DIAMOND_BLOCK:
                    break;
            }
        }
    }


    private function checkSigns() {
        $level = $this->level;
        foreach ($level->getTiles() as $tile) {
            if($tile instanceof Sign) {
                if($tile->getText()[0] == "spawn" && is_numeric($tile->getText()[1])) {
                    $tile->setText("§3[EggWars]", "§6Level: {$tile->getText()[1]}§7", "", "§o§bclick to update");
                }
            }
        }
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