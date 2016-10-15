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
    
    public $prefix = C::GRAY.C::BOLD."[".C::DARK_AQUA." EggWars ".C::GRAY."]";
    public $mode = 0;
    public $arenas = array();
    public $ingame = 0;
    public $cfg;
    public $msg;
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
                                            $arena->save();
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
                                                $this->mode = 20;
                                            }
                                            elseif($args[3]=="iron")
                                            {
                                                $this->mode = 22;
                                            }
                                            elseif($args[3]=="gold")
                                            {
                                                $this->mode = 24;
                                            }
                                        }
                                        elseif($args[2]=="spawns")
                                        {
                                            $this->mode = 1;
                                        }
                                        elseif($args[2]=="joinsign")
                                        {
                                            $this->mode = 19;
                                        }
                                        elseif($args[2]=="leavepos")
                                        {
                                            $this->mode = 18;
                                        }
                                        elseif($args[2]=="lobbypos")
                                        {
                                            $this->mode = 17;
                                        }
                                    }
                                    else
                                    {
                                        $s->sendMessage("Arena ".$args[1]." neexistuje"); 
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
    
}
