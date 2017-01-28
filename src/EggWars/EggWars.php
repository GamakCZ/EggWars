<?php
namespace EggWars;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;
use pocketmine\utils\Config;

class EggWars extends PluginBase implements Listener {
    
    public $config;
    public $levels;
    public $arenas;

    public function onEnable() {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new ItemTimer($this), 10);
        $this->saveFiles();
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

                case "red":
                    break;

                case "yellow":
                    break;

                case "green":
                    break;

                case "blue":
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
     * @return void
     */
    public function enableArenas() {
        $this->config = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        $this->arenas = $this->config->get("arenas");
        $this->levels = $this->config->get("levels");
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
     * @return array
     */
    public function getBreakableBlocks() {
        $blocks = [
                Block::SANDSTONE,
                Block::END_STONE,
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
        if($p->getLastDamageCause() !== null) {
            $entity = $p->getLastDamageCause()->getEntity();
            if($entity instanceof Player) {
                // death message will here
            }
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
class ItemTimer extends PluginTask {
    
    /** @var EggWars */
    public $plugin;
    
    public function __construct($plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {
        // minute timer
        $time = 0;
        $second = $time/2;
        if($time == 0) {
            $time++;
        }
        if($time == 120) {
            $time == 0;
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
