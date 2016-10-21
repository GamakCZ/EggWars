<?php

use pocketmine\plugin\PluginTask as PT;

class RefreshSigns extends PT {
  
  public $plugin;
  public $prefix;
  
  public function __construct(EggWars $plugin)
  {
    $this->plugin = $plugin;
    $this->prefix = $this->plugin->prefix;
  }
  
  public function onRun($tick)
  {
    /*
    
    */
  }
}
