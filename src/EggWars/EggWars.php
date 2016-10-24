<?php

namespace EggWars;

use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerInteractEvent as IE;
use pocketmine\event\player\PlayerChatEvent as CHE;
use pocketmine\level\Level;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\scheduler\PluginTask;

class EggWars extends PB implements L {
    
    public $prefix;/*= C::GRAY.C::BOLD."[".C::DARK_AQUA." EggWars ".C::GRAY."]";*/
    public $mode = 0;
    //public $arenas = array();
    public $ingame;
    public $cfg;
    public $msg;
    public $map;
    public $arena;
    /*        
    public $redegg;
    public $blueegg;
    public $yellowegg;
    public $greenegg;
    */
            
    public function onEnable()
    {
        $this->getLogger()->info("EggWars loaded");
        $this->getServer()->getPluginManager()->registerEvents($this ,$this);
        @mkdir($this->getDataFolder());
        /*$config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
        if($config->get("arenas")!=null)
        {
            $this->arenas = $config->get("arenas");     
        }
        foreach($this->arenas as $arena)
        {
            $this->getServer()->loadLevel($arena);
        }*/
        $cfg = new Config($this->getDataFolder()."/config.yml", Config::YAML);
        if($cfg->get("lang") = null)
        {
            $this->saveDefaultConfig();
        }
        //$cfg->save();
        
        if($cfg->get("lang") = "eng")
        {
            $msg = new Config($this->getDataFolder()."languages/eng.yml", Config::YAML);
        }
        elseif($cfg->get("lang") = "ces")
        {
            $msg = new Config($this->getDataFolder()."languages/ces.yml", Config::YAML);
        }
        
        $this->prefix = $cfg->get("Prefix");
    }
    
    public function getLang()
    {
        $cfg = $this->cfg;
        
        if($cfg->get("lang") = "eng")
        {
            return "eng";
        }
        else
        {
            return "ces";
        }
    }
    
    public function onCommand(CommandSender $s, Command $cmd, $label, array $args)
    {
        
        $msg = $this->msg;
        
        switch($cmd->getName())
        {
            case "EggWars":
                if(!empty($args[0]))
                {
                    if($args[0]=="help")
                    {
                        if($s instanceof Player)
                        {
                            if($s->hasPermission("ew.cmd.help"))
                            {
                                $s->sendMessage($msg->get("op_player_help"));
                            }
                            else
                            {
                                $s->sendMessage($msg->get("default_player_help"));
                            }
                        }
                        else
                        {
                            $s->sendMessage($msg->get("console_help"));
                        }
                    }
                    elseif($args[0]=="create")
                    {
                        if($s instanceof Player)
                        {
                            if($s->hasPermission("ew.cmd.create"))
                            {
                                if(!empty($args[1]))
                                {
                                    if(!empty($args[2]))
                                    {
                                        if(file_exists($this->getDataPath()."worlds/".$args[1]))
                                        {
                                            $arena = new Config($this->getDataFolder()."arenas/".$args[2].".yml", Config::YAML);
                                            $arena->set("world", $args[1]);
                                            $arena->set("status", "set");
                                            $arena->set("mode", 1);
                                            $arena->save();
                                            $this->map = $args[2];
                                        }
                                        else
                                        {
                                            $s->sendMessage($msg->get("world_doesnot_exist"));
                                        }
                                    }
                                    else
                                    {
                                        $s->sendMessage($msg->get("usage_create"));
                                    }
                                }
                                else
                                {
                                    $s->sendMessage($msg->get("usage_create"));
                                }
                            }
                            else
                            {
                                $s->sendMessage("not_permissions");
                            }
                        }
                        else
                        {
                            $s->sendMessage("console_help");
                        }
                    }
                    elseif($args[0]=="set")
                    {
                        if($s instanceof Player)
                        {
                            if($s->hasPermission("ew.cmd.set"))
                            {
                                if(!empty($args[1]))
                                {
                                    $test = new Config($this->getDataFolder()."/arenas".$args[1].".yml", Config::YAML);
                                    if($test !=null)
                                    {
                                        if($args[2]=="help")
                                        {
                                            $s->sendMessage($msg->get("setup_help"));
                                        }
                                        elseif($args[2]=="spawnsign")
                                        {
                                            if($args[3]=="bronze")
                                            {
                                                $this->mode = 8;
                                            }
                                            elseif($args[3]=="iron")
                                            {
                                                $this->mode = 9;
                                            }
                                            elseif($args[3]=="gold")
                                            {
                                                $this->mode = 10;
                                            }
                                        }
                                        elseif($args[2]=="spawns")
                                        {
                                            $this->mode = 1;
                                            $s->sendMessage("setup_spawns");
                                        }
                                        elseif($args[2]=="joinsign")
                                        {
                                            $this->mode = 5;
                                        }
                                        elseif($args[2]=="leavepos")
                                        {
                                            $this->mode = 6;
                                        }
                                        elseif($args[2]=="lobbypos")
                                        {
                                            $this->mode = 7;
                                        }
                                        elseif($args[2]=="info")
                                        {
                                            if($this->getLang()=="eng")
                                            {
                                                $s->sendMessage("You can setup arena only when is on server 1 player, else it can go to bugs");
                                            }
                                            else
                                            {
                                                $s->sendMessage("Arenu nastavuj pouze kdyz je na serveru jeden hrac, jinak to muze dojit k chybam");
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $s->sendMessage($msg->get("arena_does_not_exists")." (".$args[1].")"); 
                                    }
                                }
                                else
                                {
                                    $s->sendMessage($msg->get("setup_help"));
                                }
                            }
                            else
                            {
                                $s->sendMessage($msg->get("not_permissions"));
                            }
                        }
                        else
                        {
                            $s->sendMessage($msg->get("console_help"));
                        }
                    }
                    /*elseif($args[0]=="")
                    {
                        
                    }*/
                    else
                    {
                        $s->sendMessage($msg->get("usage"));
                    }
                }
                else
                {
                    $s->sendMessage($msg->get("usage"));
                }
        }
    }
    
    public function onInteract(IE $e)
    {
        $p = $e->getPlayer();
        $msg = $this->msg;
        $prefix = $this->prefix;
        $b = $e->getBlock();
        $level = $e->getPlayer()->getLevel();
        $t = $level->getTile($b);
        $m = $this->mode;
        
        if($p->hasPermission("ew.set"))
        {
            if($m<4 && $m>=1)
            {
                // spawns
                $m++;
                if($m = 1)
                {
                    $spawn = "blue";
                }
                elseif($m = 2)
                {
                    $spawn = "red";
                }
                elseif($m = 3)
                {
                    $spawn = "yellow";
                }
                elseif($m= 4)
                {
                    $spawn = "green";
                }
                $x = $b->getX();
                $yy = $b->getY();
                $y = $yy+1;
                $z = $b->getZ();
                $arena->set($spawn."X", $x);
                $arena->set($spawn."Y", $y);
                $arena->set($spawn."Z", $z);
                $arena->save();
                $p->sendMessage($msg->get("registered_spawn").$spawn);
                if($m = 4)
                {
                    $m = 0;
                }
            }
            elseif($m = 5)
            {
                //joinsign
                if($t instanceof Sign)
                {
                    $text = $t->getText();
                    $t->setText($this->signprefix, "Â§9[ Â§30 / 16 Â§9]", "Â§a§lJoin", "Â§8map: Â§7".$this->map);
                    $this->map = "";
                }
                /**
                uprav to k tomu kdyz clovek se pripoji na mapu 
                public function onJoin($event){
                $player = $event->getPlayer();
                $name = $player->getName();
                $this->getServer()->broadcastMessage("$name join to EggWars game!");**/
                
            }
        }
    }
    
}
