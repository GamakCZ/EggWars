<?php

namespace EggWars;

use EggWars\Arena\Arena;
use EggWars\Event\EventListener;
use EggWars\Task\Task;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
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
        if(!file_exists($this->getDataFolder()."languages")) {
            @mkdir($this->getDataFolder()."languages");
        }
        if(!is_file($this->getDataFolder()."languages/{$this->getConfig()->get("language")}.yml")) {
            $this->saveResource("languages/{$this->getConfig()->get("language")}.yml");
        }
    }

    /**
     * @param $msg
     * @return mixed|string
     */
    public static function translateMsg($msg, $prefix = true) {
        $cfg = new Config(Server::getInstance()->getDataPath()."plugins/EggWars/config.yml", Config::YAML);
        $data = new Config(Server::getInstance()->getDataPath()."plugins/EggWars/languages/".$cfg->get("language").".yml", Config::YAML);
        $msg = $data->get($msg);
        $msg = str_replace("&","§",$msg);
        if($prefix == true) {
            return self::$prefix.$msg;
        } else {
            return $msg;
        }
    }

    public function onCommand(CommandSender $sender, Command $cmd, $label, array $args) {
        if($sender instanceof Player) {
            switch ($cmd->getName()) {
                case "eggwars":
                    if(empty($args[0])) {
                        $sender->sendMessage(self::translateMsg("cmd.usage"));
                        break;
                    }
                    switch (strtolower($args[0])) {
                        case "help":
                            if(!$sender->hasPermission("ew.cmd.help")) {
                                $sender->sendMessage(self::translateMsg("cmd.noperm"));
                                break;
                            }
                            if(isset($args[1]) && in_array($args[1], ["1"])) {
                                $sender->sendMessage(self::translateMsg("cmd.help.{$args[1]}"));
                                break;
                            }
                            else {
                                $sender->sendMessage(self::translateMsg("cmd.help.1"));
                            }
                            break;
                        case "addlevel":
                            if(!$sender->hasPermission("ew.cmd.addlevel")) {
                                $sender->sendMessage(self::translateMsg("cmd.noperm"));
                                break;
                            }
                            if(empty($args[1])) {
                                $sender->sendMessage(self::translateMsg("cmd.addlevel.usage"));
                                break;
                            }
                            if($this->arena->levelExists($args[1])) {
                                $sender->sendMessage(str_replace("%1",$args[1], self::translateMsg("cmd.addlevel.nolevel")));
                                break;
                            }
                            $this->arena->addMap($this->getServer()->getLevelByName($args[1]));
                            $sender->sendMessage(str_replace("%1", $args[1], self::translateMsg("cmd.addlevel.sucess")));
                            break;
                        case "setlevel":
                            if(!$sender->hasPermission("ew.cmd.setlevel")) {
                                $sender->sendMessage(self::translateMsg("cmd.noperm"));
                                break;
                            }
                            if(empty($args[1])) {
                                $sender->sendMessage(self::translateMsg("cmd.setlevel.usage"));
                                break;
                            }
                            break;
                        case "setlobby":
                            if(!$sender->hasPermission("ew.cmd.setlobby")) {
                                $sender->sendMessage(self::translateMsg("cmd.noperm"));
                                break;
                            }

                    }
                    break;
            }
        }
    }
}
