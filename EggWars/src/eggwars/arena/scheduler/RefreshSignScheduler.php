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

namespace eggwars\arena\scheduler;

use eggwars\arena\Arena;
use eggwars\EggWars;
use eggwars\position\EggWarsPosition;
use eggwars\scheduler\EggWarsTask;
use pocketmine\level\Position;
use pocketmine\tile\Sign;

/**
 * Class RefreshSignScheduler
 * @package eggwars\arena\scheduler
 */
class RefreshSignScheduler extends EggWarsTask {

    const SIGN_PX = "§3§lEggWars";
    const SIGN_INGAME = "§5InGame";
    const SIGN_JOIN = "§aJoin";
    const SIGN_FULL = "§eFull";
    const SIGN_SETUP = "§4Setup";
    const SIGN_RESTART = "§cRestarting ...";
    const SIGN_STATUS = "§9[ §b%1 / %2§9 ]";
    const SIGN_LAST = "§7§oClick to join!";

    /** @var Arena $arena */
    private $arena;

    /**
     * RefreshSignScheduler constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $signPos = EggWarsPosition::fromArray($this->arena->arenaData["sign"], $this->arena->arenaData["sign"][3]);
        if(!$signPos instanceof Position) {
            return;
        }

        if($signPos->getLevel() == null) {
            return;
        }

        /** @var Sign $sign */
        if(($sign = $signPos->getLevel()->getTile($signPos->asVector3())) instanceof Sign) {
            $line1 = "";
            $line2 = "";
            $line3 = "";
            $line4 = "";

            //line 1
            $line1 = self::SIGN_PX;

            //line 2
            if($this->getArena()->getPhase() == 2) $line2 = self::SIGN_RESTART;
            if($this->getArena()->getPhase() == 1) $line2 = self::SIGN_INGAME;
            if(!$this->getArena()->isEnabled()) $line2 = self::SIGN_SETUP;
            if($this->getArena()->getPhase() == 0 &&
                $this->getArena()->isEnabled()) $line2 = self::SIGN_JOIN;
            if($this->getArena()->getPhase() == 0 &&
                $this->getArena()->isEnabled() &&
                count($this->getArena()->getAllPlayers()) > count($this->getArena()->arenaData["teams"])*$this->getArena()->arenaData["playersPerTeam"]) $line2 = self::SIGN_FULL;

            //line 3
            $text = self::SIGN_STATUS;

            $text = str_replace("%1", count($this->getArena()->getAllPlayers()), $text);
            $text = str_replace("%2", count($this->getArena()->arenaData["teams"])*$this->getArena()->arenaData["playersPerTeam"], $text);

            $line3 = $text;

            // line 4
            if(!$this->getArena()->isEnabled() || $this->getArena()->getPhase() == 0) {
                $line4 = self::SIGN_LAST;
            }
            else {
                $line4 = $this->getArena()->voteManager->getMap()->getCustomName();
            }

            $sign->setText($line1, $line2, $line3, $line4);
        }
    }

    /**
     * @return EggWars
     */
    public function getPlugin(): EggWars {
        return $this->getArena()->getPlugin();
    }

    /**
     * @return Arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}