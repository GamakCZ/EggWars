<?php

/*
 *    _____                __        __
 *   | ____|  __ _    __ _ \ \      / /__ _  _ __  ___
 *   |  _|   / _` | / _` |  \ \ /\ / // _` || '__|/ __|
 *   | |___ | (_| || (_| |   \ V  V /| (_| || |   \__ \
 *   |_____| \__, | \__, |    \_/\_/  \__,_||_|   |___/
 *           |___/  |___/
 */

declare(strict_types = 1);

namespace eggwars\arena\scheduler;

use eggwars\arena\Arena;
use eggwars\EggWars;
use eggwars\LevelManager;
use eggwars\scheduler\EggWarsTask;
use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\tile\Sign;
use function Sodium\library_version_major;

/**
 * Class GeneratorScheduler
 * @package eggwars\arena\scheduler
 */
class GeneratorScheduler extends EggWarsTask implements Listener {

    // line 1
    const LINE_1 = "§0§lGenerator";

    // line 2
    const LINE_2_DIAMOND = "§bDiamond";
    const LINE_2_GOLD = "§6Gold";
    const LINE_2_IRON = "§7Iron";

    // line 3
    const LINE_3 = "§0Level %level";

    // line 4
    const LINE_4 = "§8Right click";

    const IRON = 1;
    const GOLD = 2;
    const DIAMOND = 3;


    /**
     * HOW TO SET? :D
     *
     * 1 => EggWars
     * 2 => Iron | Gold | Diamond
     * 3 => 0 | 1 | 2 | 3 | 4 <-- only at gold and iron
     *
     */

    /** @var  Arena $plugin */
    private $arena;

    /** @var null|\pocketmine\level\Level  */
    private $level;

    /** @var int $tick */
    private $tick;

    /**
     * GeneratorScheduler constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        $this->checkSigns($arena->getLevel());
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        if(!$this->getArena()->getLevel() instanceof Level) return;
        $this->tick++;
        if($this->getArena()->getPhase() == 1) {
            $this->spawn();
        }
    }

    private function spawn() {
        $this->dropIron();
        $this->dropGold();
        $this->dropDiamond();
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

    public function debug($msg) {
        $this->getArena()->getPlugin()->getLogger()->critical(strval($msg));
    }

    /**
     * @param Level $level
     * @param int $material
     * @return Sign[] $signs
     */
    public function getSigns(Level $level, int $material): array {
        /** @var Sign[] $levelSigns */
        $levelSigns = [];
        foreach ($level->getTiles() as $tile) {
            if($tile instanceof Sign && $tile->getText()[0] == self::LINE_1) {
                array_push($levelSigns, $tile);
            }
        }
        /** @var Sign[] $signs */
        $signs = [];
        foreach ($levelSigns as $sign) {
            if(($material == self::IRON) && ($sign->getText()[1] == self::LINE_2_IRON)) {
                array_push($signs, $sign);
            }
            if(($material == self::GOLD) && ($sign->getText()[1] == self::LINE_2_GOLD)) {
                array_push($signs, $sign);
            }
            if(($material == self::DIAMOND) && ($sign->getText()[1] == self::LINE_2_DIAMOND)) {
                array_push($signs, $sign);
            }
        }
        return $signs;
    }

    public function dropIron() {
        $signs = $this->getSigns($this->getArena()->getLevel(), self::IRON);
        foreach ($signs as $sign) {
            $level = intval(str_replace("§0Level ", "", $sign->getText()[2]));
            switch (strval($level)) {
                case "1":
                    // 1.5 sec
                    if($this->tick%30 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::IRON_INGOT));
                    }
                    break;
                case "2":
                    // 1 sec
                    if($this->tick%20 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::IRON_INGOT));
                    }
                    break;
                case "3":
                    // 0.5 sec
                    if($this->tick%10 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::IRON_INGOT));
                    }
                    break;
                case "5":
                    // 0.25 sec
                    if($this->tick%5 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::IRON_INGOT));
                    }
                    break;
                default:
                    $this->debug($level);
                    break;
            }
        }
    }

    public function dropGold() {
        $signs = $this->getSigns($this->getArena()->getLevel(), self::GOLD);
        foreach ($signs as $sign) {
            $level = strval(str_replace("§0Level ", "", $sign->getText()[2]));
            switch (strval($level)) {
                case "1":
                    // 2.5 sec
                    if($this->tick%50 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::GOLD_INGOT));
                    }
                    break;
                case "2":
                    // 2 sec
                    if($this->tick%40 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::GOLD_INGOT));
                    }
                    break;
                case "3":
                    // 1.5 sec
                    if($this->tick%30 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::GOLD_INGOT));
                    }
                    break;
                case "4":
                    // 1 sec
                    if($this->tick%20 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::GOLD_INGOT));
                    }
                    break;
                default:
                    $this->debug($level);
                    break;
            }
        }
    }

    public function dropDiamond() {
        $signs = $this->getSigns($this->getArena()->getLevel(), self::DIAMOND);
        foreach ($signs as $sign) {
            $level = intval(str_replace("§0Level ", "", $sign->getText()[2]));
            switch (strval($level)) {
                case "1":
                    // 10 sec
                    if($this->tick%200 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::DIAMOND));
                    }
                    break;
                case "2":
                    // 7 sec
                    if($this->tick%140 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::DIAMOND));
                    }
                    break;
                case "3":
                    // 4 sec
                    if($this->tick%80 == 0) {
                        $this->getArena()->getLevel()->dropItem($sign->asVector3(), Item::get(Item::DIAMOND));
                    }
                    break;
                default:
                    $this->debug($level);
                    break;
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
        if(!in_array(intval($lvl), [0, 1, 2, 3, 4, 5])) return;
        switch ($gen) {
            case "Iron":
                $sign->setText(self::LINE_1, self::LINE_2_IRON, str_replace("%level", $lvl, self::LINE_3), self::LINE_4);
                break;
            case "Gold":
                $sign->setText(self::LINE_1, self::LINE_2_GOLD, str_replace("%level", $lvl, self::LINE_3), self::LINE_4);
                break;
            case "Diamond":
                $sign->setText(self::LINE_1, self::LINE_2_DIAMOND, str_replace("%level", $lvl, self::LINE_3), self::LINE_4);
                break;
        }
    }

    public function onTouch(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $tile = $event->getBlock()->getLevel()->getTile($event->getBlock()->asVector3());
        if(!$tile instanceof Sign) {
            return;
        }
        if($tile->getText()[0] == self::LINE_1) {
            $player->sendMessage("");
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