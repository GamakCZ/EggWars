<?php

namespace EggWars;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat as C;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\Config;

class EggWars extends PluginBase implements Listener {

public $prefix = C::DARK_AQUA . "[EggWars]";

public function onEnable(){
 $this->getServer->getPluginManager->registerEvents($this, $this)
 $this->getLogger->info("EggWars enabled");
 $this->saveDefaultConfig();

public function onDisable(){
 $this->getLogger->info("EggWars disabled");
}

public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
 if(!$sender instanceof Player) {
  return;
 }
 switch(strotolower($args[0]="EggWars")) {
  $sender->sendMessage("Use /ew help");
  return;
  case "help":
   if(!$sender->hasPermission("ew.cmd.ophelp")) {
   $sender->sendMessage(C::GOLD . "<><><><><><><><><><>");
   $sender->sendMessage(C::GOLD . "EggWars Commands");
   $sender->sendMessage(C::GOLD . "- /ew addarena");
   $sender->sendMessage(C::GOLD . "<><><><><><><><><><>");
   return;
 }
 case "addarena":
  $sender->sendMessage("use /ew addarena <world> <teams> <playersinteams>");
  if(empty($args[1])){
   if($args[2]==5, 6, 7, 8, 9) {
   return false;
   }
   $sender->sendMessage("use /ew addarena <world> <teams> <playersinteams>");
  }
  else {
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

 public function onTranslateMessages() {
  $msgcfg = new Config($his->getDataFolder()."messages.yml,Config:::YAML");
}
