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
 
 public function onEnable()
 {
  $this->getLogger()->info("Eggs Ready");
  
  $this->getServer()->getPluginManager()->registerEvents($this ,$this);
		@mkdir($this->getDataFolder());
		$config = new Config($this->getDataFolder() . "/config.yml", Config::YAML);
  if($config->get("arenas")!=null)
		{
			$this->arenas = $config->get("arenas");
		}
		foreach($this->arenas as $arena)
		{
			$this->getServer()->loadLevel($arena);
		}
}
