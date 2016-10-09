<?php

namespace EggWars;

use pocketmine\event\Listener as L;
use pocketmine\plugin\PluginBase as PB;

use pocketmine\utils\TextFormat as C;
use pocketmine\utils\Config;

use pocketmine\event\player\PlayerInteractEvent as IE;
use pocketmine\event\player\PlayerChatEvent as CHE;

class EggWars extends PB implements L {
 
 public $prefix = C::GRAY.C::BOLD."[".C::DARK_AQUA." EggWars ".C::GRAY."]";
 
 public function onEnable()
 {
  $this->getLogger()->info("Enabled");
 }
}
