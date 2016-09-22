<?php

namespace EggWars;


use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\utils\Config;
use pocketmine\command\Command as CMD;
use pocketmine\command\CommandSender as CS;
use pocketmine\utils\TextFormat as C;
use pocketmine\entity\Villager;
use pocketmine\Player;

class EggWars extends PB implements L {
 
 public $cfg;
 public $status;
 
 public function onEnable()
 {
  $this->getServer()->getPluginManager()->registerEvents($this, $this);
  $this->getLogger()->info("EggWars loaded");
  
  $cfg = new Config($this->getDataFolder()."config.yml", Config::YAML);
 }
 
 public function onCommand(CS $s, CMD $cmd, $label, array $args)
 {
  if($s instanceof Player)
  {
   return true;
  }
  
  switch($cmd->getName())
  {
   case "eggwars":
    if($s->hasPermission("ew.cmd.op"))
    {
     if($args[0]=="help")
     {
      $s->sendMessage("----<EggWarsHelp>----");
      $s->sendMessage("> /ew create <map>");
      $s->sendMessage("> /ew set <map>");
     }
     if($args[0]=="create")
     {
      $nameofmap = $args[1];
      $config = new Config($this->getDataFolder()."/arenas/"$nameofmap".yml", Config::YAML);
      $config->set("ArenaName", $nameofmap);
      $s->sendMessage("-> Arena has been sucessfully saved!");
      $s->sendMessage("-> for set arena use /ew set");
     }
     if($args[0]=="set")
     {
      if(empty($args[1]))
      {
       $s->sendMessage(">>> EggWarsSettupHelp <<<");
       $s->sendMessage("- /ews villager");
       $s->sendMessage("- /ews gold");
       $s->sendMessage("- /ews iron");
       $s->sendMessage("- /ews bronze");
       $s->sendMessage("use /ew set 2 for more");
      }
      if($args[1]=="2")
      {
       $s->sendMessage(">>> EggWarsSettupHelp <<<");
       $s->sendMessage("- /ews setspawn");
       $s->sendMessage("- /ews setlobby");
       $s->sendMessage("- /ews setleavepos");
       $s->sendMessage("- /ews setstarttime");
      }
     }
    }
    else
    {
     $s->sendMessage(C::RED . "Unknown command. Try /help for list of commands");
    }
    
    case "eggwarssetup":
     if($s->hasPermission("ew.cmd.settup"))
     {
      if($args[0]=="villager")
      {
       if(empty($args[1]))
       {
        $s->sendMessage("> use: /ews villager");
        $s->sendMessage("-> spawn villager on your position");
       }
       else
       {
        $s->sendMessage("> villager was been spawned");
        //villager
        $vr = new Villager($this->spawnTo($s));
       }
      }
      // commands for spawn items
      if($args[0]=="gold")
      {
       if(empty($args[1]))
       {
        $s->sendMessage("> use: /ews gold <map>");
        $s->sendMessage("-> set postition to spawn gold");
       }
       else
       {
        $s->sendMessage("> gold spawn position has been set on your pos");
        $x = $s->getX();
        $y = $s->getY();
        $z = $s->getZ();
        $xyz = array($x, $y, $z);
        $map = $args[1];
        $gcfg = new Config($this->getDataFolder()."/arenas/".$map."/golds", Config::YAML);
        $gcfg->set($xyz);
       }
      }
       if($args[0]=="iron")
       {
        if(empty($args[1]))
        {
         $s->sendMessage("> use: /ews iron <map>");
         $s->sendMessage("->set position to spawn iron");
        }
        else
        {
        $s->sendMessage("> gold spawn position has been set on your pos");
        $x = $s->getX();
        $y = $s->getY();
        $z = $s->getZ();
        $xyz = array($x, $y, $z);
        $map = $args[1];
        $icfg = new Config($this->getDataFolder()."/arenas/".$map."/irons", Config::YAML);
        $icfg->set($xyz);
        }
       }
       if($args[0]=="bronze")
       {
        if(empty($args[1]))
        {
         $s->sendMessage("> use: /ews bronze <map>");
         $s->sendMessage("-> set position to spawn bronze");
        }
        else
        {
        $s->sendMessage("> bronze spawn position has been set on your pos");
        $x = $s->getX();
        $y = $s->getY();
        $z = $s->getZ();
        $xyz = array($x, $y, $z);
        $map = $args[1];
        $bcfg = new Config($this->getDataFolder()."/arenas/".$map."/bronze");
        $bcfg->set($xyz);
        }
       }
       //end of code for item spawn
       
       if($args[0]=="setspawn")
       {
        if(empty($args[1]))
        {
         $s->sendMessage("> use: /ews setspawn <map> <team>");
         $s->sendMessage("> teams: blue, red, yellow, green");
         $s->sendMessage("-> set game map spawn");
        }
        else if(empty($args[2]))
        {
         $s->sendMessage("> use: /ews setspawn <map> <team>");
         $s->sendMessage("> teams: blue, red, yellow, green");
         $s->sendMessage("-> set game map spawn");
        }
        else
        {
         $map = $args[1];
         $team = $args[2];
         $x = $s->getX();
         $y = $s->getY();
         $z = $s->getZ();
         $tcg = new Config($this->getDataFolder()."/arenas/".$map."/teams/".$team, Config::YAML);
         $tcg->set("x", $x);
         $tcg->set("y", $y);
         $tcg->set("z", $z);
        }
       }
       break;
       if($args[0]=="setlobby")
       {
        if(empty($args[1]))
        {
         $s->sendMessage("use: /ews setlobby <map>");
        }
        elseif(empty($args[2]))
        {
         
        }
       }
      }
     }
  }
 }
