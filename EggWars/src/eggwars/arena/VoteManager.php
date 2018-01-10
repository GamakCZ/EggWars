<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\level\EggWarsLevel;
use pocketmine\Player;

/**
 * Class VoteManager
 * @package eggwars\arena
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
     * @var EggWarsLevel[] $levels
     */
    private $levels = [];

    /**
     * VoteManager constructor.
     * @param Arena $arena
     * @param array $levels
     */
    public function __construct(Arena $arena, array $levels) {
        $this->arena = $arena;
        $this->levels = $levels;
        $this->maps = [$levels[0]->getLevelName() =>
            [
                "customName" => $levels[0]->getCustomName(),
                "votes" => []
            ],
            $levels[1]->getLevelName() => [
                "customName" => $levels[1]->getCustomName(),
                "votes" => []
            ],
            $levels[2]->getLevelName() => [
                "customName" => $levels[2]->getCustomName(),
                "votes" => []
            ]];
    }

    /**
     * @return string $format
     */
    public function getBarFormat(): string {
        $m = "\n".str_repeat(" ", 60);
        return "§7Voting §f| §6/vote <map>".$m.
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
        return $this->maps[$this->levels[intval($map-1)]->getLevelName()]["customName"];
    }

    /**
     * @param int $map
     * @return int $votes
     */
    public function getVotes(int $map): int {
        return count($this->maps[$this->levels[intval($map-1)]->getLevelName()]["votes"]);
    }

    /**
     * @return EggWarsLevel
     */
    public function getMap() {
        sort($array = [$this->maps[0]["customName"] => count($this->maps[0]["votes"]),
            $this->maps[1]["customName"] => count($this->maps[1]["votes"]),
            $this->maps[2]["customName"] => count($this->maps[2]["votes"])]);
        return new EggWarsLevel($this->getArena()->getPlugin()->getLevelManager()->defaultLevelData);
    }

    /**
     * @param int $map
     * @return bool
     */
    public function addVote(Player $player, int $map): bool {
        if(!in_array($map, [1,2,3])) {
            return false;
        }
        if($lastVote = $this->voted($player)) {
            unset($this->maps[intval($lastVote-1)]["votes"][$player->getName()]);
        }
        switch ($map) {
            case 1:
                array_push($this->maps[0]["votes"], $player->getName());
                return true;
            case 2:
                array_push($this->maps[1]["votes"], $player->getName());
                return true;
            case 3:
                array_push($this->maps[2]["votes"], $player->getName());
                return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return int
     */
    private function voted(Player $player): int {
        if(in_array($player->getName(), $this->maps[0]["votes"])) return 1;
        if(in_array($player->getName(), $this->maps[1]["votes"])) return 2;
        if(in_array($player->getName(), $this->maps[2]["votes"])) return 3;
        return 0;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}