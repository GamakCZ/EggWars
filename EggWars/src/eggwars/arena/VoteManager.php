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
     * VoteManager constructor.
     * @param EggWarsLevel[] $levels
     */
    public function __construct(array $levels) {
        $this->maps = [$levels[0]->getLevelName() =>
            [
                "customName" => $levels[0]->getName(),
                "votes" => []
            ],
            $levels[1]->getLevelName() => [
                "customName" => $levels[1]->getName(),
                "votes" => []
            ],
            $levels[2]->getLevelName() => [
                "customName" => $levels[2]->getName(),
                "votes" => []
            ]];
    }

    /**
     * @param int $map
     * @return bool
     */
    public function addVote(Player $player, int $map) {
        if(!in_array($map, [1,2,3])) {
            return false;
        }
        switch ($map) {
            case 1:

        }
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
}