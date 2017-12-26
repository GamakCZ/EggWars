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
    }


    private function checkSigns() {
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