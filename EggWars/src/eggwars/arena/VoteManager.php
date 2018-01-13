<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\EggWars;
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
        $this->maps = [
            $levels[0]->getCustomName() =>
            [
                "customName" => $levels[0]->getCustomName(),
                "votes" => [],
                "arg" => 0
            ],
            $levels[1]->getCustomName() => [
                "customName" => $levels[1]->getCustomName(),
                "votes" => [],
                "arg" => 1
            ],
            $levels[2]->getCustomName() => [
                "customName" => $levels[2]->getCustomName(),
                "votes" => [],
                "arg" => 2
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
        return $this->maps[$this->levels[intval($map-1)]->getCustomName()]["customName"];
    }

    /**
     * @param int $map
     * @return int $votes
     */
    public function getVotes(int $map): int {
        return count($this->maps[$this->levels[intval($map-1)]->getCustomName()]["votes"]);
    }


    /**
     * @return EggWarsLevel
     */
    public function getMap() {
        $maps = [
            $this->getMapName(1) => $this->getVotes(1),
            $this->getMapName(2) => $this->getVotes(2),
            $this->getMapName(3) => $this->getVotes(3)
        ];

        asort($maps);

        array_shift($maps);
        array_shift($maps);

        $result = null;

        foreach ($maps as $map => $votes) {
            $result = $map;
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
            unset($this->maps[$this->levels[intval($lastVote-1)]->getCustomName()]["votes"][$player->getName()]);
        }
        if(is_numeric($map)) {
            switch ($map = intval($map)) {
                case 1:
                    $this->maps[$this->levels[0]->getCustomName()]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->levels[0]->getCustomName()}!");
                    break;
                case 2:
                    $this->maps[$this->levels[1]->getCustomName()]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->levels[1]->getCustomName()}!");
                    break;
                case 3:
                    $this->maps[$this->levels[2]->getCustomName()]["votes"][$player->getName()] = 1;
                    $player->sendMessage("§aYou are voted for {$this->levels[2]->getCustomName()}!");
                    break;
            }
        }
        else {
            if($this->levels[0]->getCustomName() == $map) {
                $this->addVote($player, 1);
            }
            elseif($this->levels[1]->getCustomName() == $map) {
                $this->addVote($player, 2);
            }
            elseif($this->levels[2]->getCustomName() == $map) {
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
        if(isset($this->maps[$this->levels[0]->getCustomName()]["votes"][$player->getName()])) return 1;
        if(isset($this->maps[$this->levels[1]->getCustomName()]["votes"][$player->getName()])) return 2;
        if(isset($this->maps[$this->levels[2]->getCustomName()]["votes"][$player->getName()])) return 3;
        return 0;
    }

    /**
     * @return Arena $arena
     */
    public function getArena(): Arena {
        return $this->arena;
    }
}