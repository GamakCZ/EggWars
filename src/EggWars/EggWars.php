<?php

/**
 *  Created by @GamakCZ
 *  Website: gamakcz.github.io/EggWars
 *  For MCPE Version: 1.0
 *   - Not completed -
 */

namespace EggWars;

use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;

class EggWars extends PluginBase implements Listener {

    public $config;
    public $levels;
    public $arenas;

    public $game = [
        "levels" => [],
        "arenas" => [],
        "players" => []
    ];

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ItemTimer($this), 10);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new PopupTask($this), 20);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), 20);
        $this->saveFiles();
        $this->enableArenas();
    }

    public function saveFiles() {
        if(!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml", true);
        }
        if(!file_exists($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder());
        }
        if(is_file($this->getDataFolder()."data.yml")) {
            $this->saveResource("data.yml");
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
     * @param CommandSender $s
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return void
     */
    public function onCommand(CommandSender $s, Command $cmd, $label, array $args) {
        if(strtolower($cmd->getName()) == "ew") {
            switch(strtolower($args[0])) {
                case "help":
                    if($s instanceof Player) {
                        if($s->isOp()) {
                            // will gets form config
                            $msg1 = "";
                            $msg2 = "";
                            $msg3 = "";
                            $s->sendMessage("§8----- {$this->prefix()}§8-----");
                            $s->sendMessage("§7/ew help :§8 {$msg1}");
                            $s->sendMessage("§7/ew set :§8 {$msg2}");
                            $s->sendMessage("§7/ew create :§8 {$msg3}");
                            $s->sendMessage("§8------------------------------");
                        }
                    }
                    break;

                case "set":
                    if($s instanceof Player) {
                        if($s->isOp()) {
                            switch(strtolower($args[1])) {
                                case "help":
                                    break;
                                case "spawn":
                                    break;
                                case "villager":
                                    break;
                                case "egg":
                                    break;
                            }
                        }
                    }
                    break;

                case "create":
                    if($s instanceof Player) {
                        if($s->isOp()) {
                            switch(strtolower($args[1])) {

                            }
                        }
                    }
                    break;

                case "join":
                    if($s instanceof Player) {
                        if(isset($args[1]) && empty($args[2])) {

                        }
                        elseif(isset($args[1]) && isset($args[2])) {
                            if($s->isOp()) {

                            }
                        }
                    }
                    break;

                case "leave":
                    if($s instanceof Player) {
                        if(isset($args[1]) && empty($args[2])) {

                        }
                        elseif(isset($args[1]) && isset($args[2])) {
                            if($s->isOp()) {

                            }
                        }
                    }
                    break;

                case "yellow":
                    $this->joinToTeam($s, 1);
                    break;
                case "red":
                    $this->joinToTeam($s, 2);
                    break;
                case "blue":
                    $this->joinToTeam($s, 3);
                    break;
                case "green":
                    $this->joinToTeam($s, 4);
                    break;
            }
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
        $levels = array_push($arenas->get("levels"), $this->getWaitingLevel()->getName());
        foreach($levels as $level) {
            if($p->getLevel()->getName() == $level) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @param Player $p
     * @return bool
     */
    public function inLobby(Player $p) {
        $lobby = $this->getWaitingLevel()->getName();
        if($p->getLevel()->getName() == $lobby) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return void
     */
    public function enableArenas() {
        $this->config = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        $this->arenas = $this->config->get("arenas");
        $this->levels = $this->config->get("levels");
        $this->game["levels"] = $this->levels;
        $this->game["arenas"] = $this->arenas;
    }

    public function getTeamByPlayer(Player $p) {

    }

    public function getPlayersByTeam($team) {
        
    }

    public function hasEgg(Player $p) {

    }

    public function joinToTeam(Player $p, $team) {

    }

    /**
     * @return Level
     */
    public function getWaitingLevel() {
        return $this->getServer()->getLevelByName("EggWarsLobby");
    }

    /**
     * @return array
     */
    public function getBreakableBlocks() {
        $blocks = [
                Block::get(Block::SANDSTONE),
                Block::get(Block::ENDSTONE),
                Block::OBSIDIAN,
                Block::CHEST
                  ];
        return $blocks;
    }
    ###
    #   Events
    ##

    public function onDeath(PlayerDeathEvent $e) {
        $p = $e->getPlayer();
         $entity = $p->getLastDamageCause()->getEntity();
            if($entity instanceof Player) {
                // death message will here
            }
        }
    }

    public function onDamage(EntityDamageEvent $e) {
        $p = $e->getEntity();
        if($this->inGame($p)) {

        }
    }

    public function onInteract(PlayerInteractEvent $e) {
        $p = $e->getPlayer();
        $b = $e->getBlock();
        $t = $p->getLevel()->getTile($b);
        if($t instanceof Sign) {
            $text = $t->getText();
            if($text[0]=="EggWars") {
                switch($text[1]) {
                    case "iron":
                        $t->setText("§l{$this->prefix()}","Level 1","§l§7Iron", "§8generator");
                        break;
                    case "gold":
                        $t->setText("§l{$this->prefix()}","Level 1","§l§6Gold", "§8generator");
                        break;
                    case "diamond":
                        switch($text[2]) {
                            case "0":
                                $t->setText("§l{$this->prefix()}","Level 0","§l§bDiamond", "§8generator");
                                break;
                            case "1":
                                $t->setText("§l{$this->prefix()}","Level 1","§l§bDiamond", "§8generator");
                                break;
                        }
                        break;
                }
            }
            elseif($text[0]=="§l{$this->prefix()}") {
                if($this->inGame($p)) {
                    $data = new Config($this->getDataFolder()."/data.yml", Config::YAML);
                    switch($text[2]) {
                        case "§l§7Iron":
                            $item = $p->getInventory()->getItem(Item::IRON_BAR);
                            $level = str_replace("Level ", "", $text[1]);
                            $upgrade = $data->get();
                            if($item->count >= $upgrade);
                            break;
                        case "§l§6Gold":
                            if($p->getInventory())
                                break;
                    }
                }
            }
        }
    }
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
class Task extends PluginTask {

    /** @var EggWars */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {

    }
}

class PopupTask extends PluginTask {

    /** @var  EggWars */
    public $plugin;
    public $prefix;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->prefix = $this->plugin->prefix();
    }

    public function onRun($currentTick) {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $p) {
            if($this->plugin->inGame($p)) {
                if($this->plugin->inLobby($p)) {
                    $map1 = "";
                    $map2 = "";
                    $map3 = "";
                    $votes1 = 0;
                    $votes2 = 0;
                    $votes3 = 0;

                    $p->sendTip("                                 §6Voting §8| §7/vote <map>\n
                                 §b[1] §c» §7{$map1} §7({$votes1})\n
                                 §b[1] §c» §7{$map2} §7({$votes2})\n
                                 §b[1] §c» §7{$map3} §7({$votes3})\n
                                ");
                }
                else {
                    $map = array();

                    $true = "§a✔";
                    $false = "§c✖";

                    $yellow = $true;
                    $red = $true;
                    $blue = $true;
                    $green = $true;

                    $p->sendTip("                                 §7Map: §6{$map}\n
                                 §eYellow: {$yellow}\n
                                 §cRed: {$red}\n
                                 §9Blue: {$blue}\n
                                 §aGreen: {$green}");
                }
            }
        }
    }
}

class ItemTimer extends PluginTask {

    /** @var EggWars */
    public $plugin;

    public $tick;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {
        // minute timer
        #$tick = 0;
        $tick = $currentTick;
        $second = $tick/2;
        if($tick == 0) {
            $tick++;
        }
        if($tick == 120) {
            $tick == 0;
        }

        foreach($this->plugin->levels as $name) {
            $level = $this->plugin->getServer()->getLevelByName($name);
            foreach($level->getPlayers() as $p) {
                if($p !== null) {
                    foreach($level->getTiles() as $tile) {
                        if($tile instanceof Sign) {

                        }
                    }
                }
            }
        }
    }
}
