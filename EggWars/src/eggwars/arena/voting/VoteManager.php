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

namespace eggwars\arena\voting;

use eggwars\arena\Arena;
use eggwars\level\EggWarsLevel;
use pocketmine\Player;

/**
 * Class VoteManager
 * @package eggwars\arena\voting
 */
class VoteManager {

    /**
     * @var array $maps
     */
    public $maps = [];

    /**
     * @var Arena $arena
     */
    public $arena;

    /**
     * VoteManager constructor.
     * @param Arena $arena
     * @param array $levels
     */
    public function __construct(Arena $arena, array $levels) {
        $this->arena = $arena;
        $this->levels = $levels;
        $this->createVoteTable($levels);
    }

    /**
     * @param array $levels
     */
    public function createVoteTable(array $levels) {
        $this->maps = [
            0 => [
                    "customName" => $levels[0]->getCustomName(),
                    "votes" => []
                ],
            1 => [
                "customName" => $levels[1]->getCustomName(),
                "votes" => []
            ],
            2 => [
                "customName" => $levels[2]->getCustomName(),
                "votes" => []
            ]];
    }

    /**
     * @return string $format
     */
    public function getBarFormat(): string {
        $m = "\n".str_repeat(" ", 80);
        return $m."§8Voting §f| §6/vote <map>".$m.
            "§b[1] §7{$this->getMapName(1)} §c» §a{$this->getVotes(1)} Votes".$m.
            "§b[2] §7{$this->getMapName(2)} §c» §a{$this->getVotes(2)} Votes".$m.
            "§b[3] §7{$this->getMapName(3)} §c» §a{$this->getVotes(3)} Votes";
    }

    /**
     * @param int $map
     * @return string $name
     */
    public function getMapName(int $map): string {
        #return $this->maps[intval($map-1)]["customName"];
        #return $this->maps[$this->levels[intval($map-1)]->getCustomName()]["customName"];
        return $this->maps[intval($map-1)]["customName"];
    }

    /**
     * @param int $map
     * @return int $votes
     */
    public function getVotes(int $map): int {
        #return count($this->maps[$this->levels[intval($map-1)]->getCustomName()]["votes"]);
        return count($this->maps[intval($map-1)]["votes"]);
    }


    /**
     * @return EggWarsLevel
     */
    public function getMap() {

        $a = $this->getMapName(1);
        $b = $this->getMapName(2);
        $c = $this->getMapName(3);

        $maps = [
            "a" => $this->getVotes(1),
            "b" => $this->getVotes(2),
            "c" => $this->getVotes(3)
        ];

        asort($maps);

        $result = null;

        foreach ($maps as $map => $votes) {
            $result = ${$map};
        }

        if($result == null) {
            $result = $this->getMapName(1);
        }

        return $this->getArena()->getPlugin()->getLevelManager()->getLevelByName($result);
    }

    /**
     * @param int|string $map
     * @return bool
     */
    public function addVote(Player $player, $map): bool {
        $lastVote = $this->voted($player);
        if($lastVote !== 0) {
            unset($this->maps[intval($lastVote-1)]["votes"][$player->getName()]);
        }
        if(is_numeric($map)) {
            switch ($map = intval($map)) {
                case 1:
                    $this->maps[0]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->maps[0]["customName"]}!");
                    break;
                case 2:
                    $this->maps[1]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->maps[1]["customName"]}!");
                    break;
                case 3:
                    $this->maps[2]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->maps[2]["customName"]}!");
                    break;
            }
        }
        else {
            if($this->maps[0]["customName"] == $map) {
                $this->addVote($player, 1);
            }
            elseif($this->maps[0]["customName"] == $map) {
                $this->addVote($player, 2);
            }
            elseif($this->maps[0]["customName"] == $map) {
                $this->addVote($player, 3);
            }
            else {
                $player->sendMessage("§cMap {$map} does not found");
            }
        }
        return false;
    }

    /**
     * @param Player $player
     * @return int
     */
    private function voted(Player $player): int {
        if(isset($this->maps[0]["votes"][$player->getName()])) return 1;
        if(isset($this->maps[1]["votes"][$player->getName()])) return 2;
        if(isset($this->maps[2]["votes"][$player->getName()])) return 3;
        return 0;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}