<?php

namespace EggWars\Task;

use EggWars\Arena\Arena;
use EggWars\EggWars;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class RefreshSign extends PluginTask {

    /** @var EggWars */
    public $plugin;

    /** @var  Task */
    public $task;
    
    /** @var  Arena */
    public $arena;

    public $genPX = "§9§l[Generator]";
    public $genIron = "§7§lIron";
    public $genGold = "§l§6Gold";
    public $genDiamond = "§b§lDiamond";

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->task = $this->plugin->task;
        $this->arena = $this->task->getArena();
        parent::__construct($plugin);
    }

    public function getServer() {
        return $this->plugin->getServer();
    }

    public function onRun($currentTick) {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getTiles() as $tile) {
                if($tile instanceof Sign) {
                    $text = $tile->getText();
                    // generators
                    if($text[0]=="[GEN]") {
                        switch ($text[1]) {
                            case "iron":
                                if(in_array($text[2],["broken","1","2","3","4","5"])) {
                                    $tile->setText($this->genPX,$this->genIron,"§7Level: §8{$text[2]}");
                                }
                                else {
                                    $tile->setText($this->genPX,$this->genIron,"§7Level: §8broken");
                                }
                                break;
                            case "gold":
                                if(in_array($text[2],["broken","1","2","3","4","5"])) {
                                    $tile->setText($this->genPX,$this->genGold,"§7Level: §8{$text[2]}");
                                }
                                else {
                                    $tile->setText($this->genPX,$this->genGold,"§7Level: §8broken");
                                }
                                break;
                            case "diamond":
                                if(in_array($text[2],["broken","1","2","3"])) {
                                    $tile->setText($this->genPX,$this->genDiamond,"§7Level: §8{$text[2]}");
                                }
                                else {
                                    $tile->setText($this->genPX,$this->genDiamond,"§7Level: §8broken");
                                }
                                break;
                            default:
                                $tile->setText($this->genPX,$this->genIron,"§7Level: §8broken");
                                break;

                        }
                    }
                    if($text[0]=="[EW]") {
                        if(isset($text[1]) && $this->arena->arenaExists($text[1])) {
                            $tile->setText(EggWars::$signprefix,"§7Status: §cSetup", "§6§l[ §r§70 / 16 §6§l]", "§7Map: §aLobby");
                        }
                        else {
                            $tile->setText(EggWars::$prefix."§cArena does not exists!");
                        }
                    }
                }
            }
        }
    }
}
