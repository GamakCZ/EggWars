<?php

namespace EggWars;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Config;

class EggWars extends PluginBase implements Listener {
    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveFiles();
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), $tick);
    }
    public function saveFiles() {
        if(!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml", true);
        }
        if(!is_file($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder());
        }
        if(!file_exists($this->getDataFolder()."languages")) {
            @mkdir($this->getDataFolder()."languages");
        }
        if(!is_file($this->getDataFolder()."arenas")) {
            $arenas = new Config($this->getDataFolder()."/arenas.yml", Config::YAML);
            $arenas->set("arenas", array());
            $arenas->set("levels", array());
            $arenas->save();
        }

        // language support
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        if(!is_file($this->getDataFolder()."languages/{$this->getLang()}.yml")) {
            $this->saveResource("languages/{$this->getLang()}.yml");
        }
        if(!is_file($this->getDataFolder()."languages/{$this->getLang()}.yml")) {
            $this->getLogger()->warning($this->prefix()." Language {$this->getLang()} does not found!");
            $this->getLogger()->info("Setting language to English.");
            $cfg->set("lang", "English");
        }
    }

    /**
     * @return string
     */
    public function prefix() {
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        return "{$cfg->get("prefix")} §7";
    }

    /**
     * @return bool|mixed
     */
    public function getLang() {
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        return $cfg->get("lang");
    }

    /**
     * @param $message
     * @return bool|mixed
     */
    public function getMsg($message) {
        $msg = new Config($this->getDataFolder()."languages/{$this->getLang()}.yml", Config::YAML);
        return $msg->get($message);
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function inGame(Player $p) {
        $arenas = new Config($this->getDataFolder()."/arenas.yml", Config::YAML);
        foreach($arenas->get("levels") as $level) {
            if($p->getLevel()->getName() == $level) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    /**
     * @return array
     */
    public function getBreakableBlocks() {
        $blocks = array(Block::SANDSTONE,
                        Block::END_STONE,
                        Block::OBSIDIAN,
                        Block::CHEST);
        return $blocks;
    }
    
    ###
    #   Events
    ##
    public function onPlace(BlockPlaceEvent $e) {
        $p = $e->getPlayer();
        if($this->inGame($p)) {
            foreach($this->getBreakableBlocks() as $breakableBlock) {
                if($e->getBlock()->getId()!==$breakableBlock) {
                    $e->setCancelled();
                    return;
                }
            }
        }
    }
    public function onBreak(BlockBreakEvent $e) {
        $p = $e->getPlayer();
        if($this->inGame($p)) {
            foreach($this->getBreakableBlocks() as $breakableBlock) {
                if($e->getBlock()->getId()!==$breakableBlock) {
                    $e->setCancelled();
                    return;
                }
            }
        }
    }
}
