<?php

namespace EggWars;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\block\Block;
use pocketmine\item\Item;

class EggWars extends PluginBase implements Listener {

public $prefix = C::DARK_AQUA . "[EggWars]";

public function onEnable(){
 $this->getServer->getPluginManager->registerEvents($this, $this)
 $this->getLogger->info("EggWars enabled");
 $this->saveDefaultConfig();
 $mcfg = new Config($this->getDataFolder()."messages.yml, Config::YAML");

public function onDisable(){
 $this->getLogger->info("EggWars disabled");
}

public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
 if(!$sender instanceof Player) {
  return;
 }
 switch(strotolower($args[0]=="EggWars")) {
  $sender->sendMessage("Use /ew help");
  return;
  case "help":
   if(!$sender->hasPermission("ew.cmd.ophelp")) {
   $sender->sendMessage(C::GOLD . "<><><><><><><><><><>");
   $sender->sendMessage(C::GOLD . "EggWars Commands");
   $sender->sendMessage(C::GOLD . "- /ew addarena");
   $sender->sendMessage(C::GOLD . "- /ew regsign")
   $sender->sendMessage(C::GOLD . "<><><><><><><><><><>");
   return;
   
 } else {
  $sender->sendMessage(C::GOLD . "<><><><><><><><><><><>");
  $sender->sendMessage(C::GOLD . "EggWars Commands");
  $sender->sendMessage(C::GOLD . /*"- /ew join"*/);
  $sender->sendMessage(C::GOLD . "<><><><><><><><><><><>");
 }
 case "addarena":
  if(!$sender->hasPermission"ew.cmd.addarena") {
  $sender->sendMessage("use /ew addarena <world> <teams> <playersinteams>");
  if(empty($args[1])){
   if($args[2]==1, 2, 3, 5, 6, 7, 8, 9) {
    $sender->sendMessage("teams must be 2, 3, 4");
   return false;
   }
    if($args[3]==1,2,3, 5, 6, 7, 8, 9) {
     $sender->sendMessage("limit is 4 players");
    }
   $sender->sendMessage("use /ew addarena <world> <teams> <playersinteams>");
  }
  else {
   $sender->sendMessage($this->$prefix . "Arena was been saved");
   $sender->sendMessage($this->$prefix . "now register team signs using /ew regsign");
  $wd = $args[1];
  $ts = $args[2];
  $pit = $args[3];
  
  $cfg = new Config($this->getDataFolder()."Arenas/".$wd.".yml", Config::YAML);
  if($cfg->get("name")==null) {
   $cfg->set("name", $wd);
   $cfg->set("Teams", $ts);
   $cfg->set("PlayersInTeams", $pit);
    }
   }
  } 
 case "regsign":
  if(!sender->hasPermission("ew.cmd.regsign")) {
  $sender->sendMessage("use /ew regsign <JoinSign|TeamSign> <Arena>");
  $sender->sendMessage("stand to x, y, z sign")
  if($args[2]=="TeamSign") {
   $sender->sendMessage("use /ew regsign <TeamSign> <Arena> <Team>")
   if($args[3]==null) {
    return false;
   } else {
   $arenaname = $args[3];
   $team = $args[4];
   $arena = $this->getDataFolder()."Arenas/".$arenaname."yml";
   $sx = $this->$x;
   $sy = $this->$y;
   $sz = $this->$z
    $arena->set("### TeamSigns");
    $arena->set("###");
    $arena->set("Team", $team);
    $arena->set("X", $sx);
    $arena->set("Y", $sy);
    $arena->set("Z", $sz);
    $arena->set("###");
     }
    }
   }
   
   case "info":
    if(!sender->hasPermission("ew.cmd.info")) {
     $sender->sendMessage("***************************");
     $sender->sendMessage("- Plugin created by GamakCZ");
     $sender->sendMessage("- Download on: bit.do/gamcz");
     $sender->sendMessage("***************************");
     
     break;
   }
  }
 }

 public function onTranslateMessages() {
  
  $mcfg = Config($this->getDataFolder()."messages.yml");
  
  $mcfg->set("msg.join", "has join to the EggWars game");
  $mcfg->set("msg.leave", "has left the EggWars game");
  $mcfg->set("msg.win", "team has won the EggWars game");
  $mcfg->set("msg.eggbroke", "egg was been broke");
  $mcfg->set("msg.teamjoin.one", "you are in ");
  $mcfg->set("msg.teamjoin.two", " team");
 }
 
 public function onJoinTeam() {
  $tjo = $this->getConfig()->get("msg.teamjoin.one");
  $tht = $this->getConfig()->get("msg.teamjoin.two");
 }
}
