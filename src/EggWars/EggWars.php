<?php

namespace EggWars;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class EggWars extends PluginBase implements Listener {

public function onEnable(){
 $this->getServer->getPluginManager->registerEvents($this, $this)
 $this->getLogger->info(EggWars enabled);
