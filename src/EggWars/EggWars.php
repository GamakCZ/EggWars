<?php

namespace EggWars;


use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;
use pocketmine\utils\Config;
use pocketmine\command\Command as CMD;
use pocketmine\command\CommandSender as CS;
use pocketmine\utils\TextFormat as C;
use pocketmine\entity\Villager;

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
     if($s->hasPermission("ew.cmd.op"))
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
        $xyz = array($x, $y, $z);
        $x = $s->getX();
        $y = $s->getY();
        $z = $s->getZ();
        $gcfg = new Config($this->getDataFolder()."/arenas/".$map."/golds");
        $gcfg->set($xyz);
       }
      }
     }
  }
 }
 
 
}
