<?php

namespace EggWars;

use EggWars\Arena\Arena;
use EggWars\Event\EventListener;
use EggWars\Task\Task;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class EggWars extends PluginBase{

    /** @var  EventListener */
    public $listener;

    /** @var  Task */
    public $task;

    /** @var  Arena */
    public $arena;

    public static $prefix;
    public static $signprefix;

    public function onEnable() {
        $this->registerClasses();
        $this->initConfig();
        $this->task->registerTasks();
        $this->getServer()->getPluginManager()->registerEvents($this->listener,$this);
        self::$prefix = $this->getConfig()->get("prefix")." §r§7";
        self::$signprefix = $this->getConfig()->get("sign-prefix");
    }

    public function registerClasses() {
        $this->listener = new EventListener($this);
        $this->task = new Task($this);
        $this->arena = new Arena($this);
    }

    public function initConfig() {
        if(!file_exists($this->getDataFolder())) {
            @mkdir($this->getDataFolder());
        }
        if(!file_exists($this->getDataFolder()."arenas")) {
            @mkdir($this->getDataFolder()."arenas");
        }
        if(!file_exists($this->getDataFolder()."levels")) {
            @mkdir($this->getDataFolder()."levels");
        }
        if(!is_file($this->getDataFolder()."/config.yml")) {
            $this->saveResource("/config.yml");
        }
        if(!is_file($this->getDataFolder()."arenas/default.yml")) {
            $this->saveResource("/default.yml");
        }
    }

    /**
     * @param $msg
     * @return mixed|string
     */
    public static function translateMsg($msg, $prefix = true) {
        $cfg = new Config(self::getDataFolder()."languages/".self::getConfig()->get("language").".yml", Config::YAML);
        $msg = $cfg->get($msg);
        $msg = str_replace("&","§",$msg);
        if($prefix == true) {
            return self::$prefix.$msg;
        }
        else {
            return $msg;
        }
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if($sender instanceof Player) {
            switch ($cmd->getName()) {
                case "eggwars":
                    switch (strtolower($args[0])) {
                        case "help":
                            if(!$sender->isOp()) {
                                $sender->sendMessage(self::translateMsg("cmd.noperm"));
                                break;
                            }

                            break;
                    }
                    break;
            }
        }
    }
}
