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
            $this->getLogger()->debug("§6Saving config!");
        }
        if(!is_file($this->getDataFolder()."arenas/default.yml")) {
            $this->saveResource("/default.yml");
        }
        if(!file_exists($this->getDataFolder()."languages")) {
            @mkdir($this->getDataFolder()."languages");
        }
        if(!is_file($this->getDataFolder()."languages/English.yml")) {
            $this->saveResource("languages/English.yml");

        }
    }

    /**
     * @param $message
     * @param $prefix
     * @return bool|mixed|string
     */
    public function translateMsg($message, $prefix) {
        // config
        $lang = new Config($this->getDataFolder()."/languages/English.yml", Config::YAML);
        // from cofig
        $data = $lang->get($message);
        // message
        $msg = str_replace("&", "§", $data);

        if($prefix == true) {
            //return
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
                    if(empty($args[0])) {
                        $sender->sendMessage(self::$prefix."§7Usage: §c/ew help");
                        break;
                    }
                    switch (strtolower($args[0])) {
                        case "help":
                            if(!$sender->hasPermission("ew.cmd.help")) {
                                $sender->sendMessage("§cYou have not permissions to use this command!");
                                break;
                            }
                            if(isset($args[1]) && in_array($args[1], ["1"])) {
                                $sender->sendMessage("§7----- §8[ §6EggWars §8] §7-----\n".
                                "§2/ew help §9Displays EggWars help menu\n".
                                "§2/ew addlevel §9Add game level\n".
                                "§2/ew setlevel §9Set level data\n".
                                "§2/ew setlobby §9Set waiting lobby position");
                                break;
                            }
                            else {
                                $sender->sendMessage("§7----- §8[ §6EggWars §8] §7-----\n".
                                    "§2/ew help §9Displays EggWars help menu\n".
                                    "§2/ew addlevel §9Add game level\n".
                                    "§2/ew setlevel §9Set level data\n".
                                    "§2/ew setlobby §9Set waiting lobby position");
                            }
                            break;
                        case "addarena":
                            if(!$sender->hasPermission("ew.cmd.addarena")) {
                                $sender->sendMessage("§cYou have not permissions to use this command!");
                                break;
                            }
                            if(empty($args[1])) {
                                $sender->sendMessage(self::$prefix."§7Usage:§c /ew addarena <arena>");
                                break;
                            }
                            $this->arena->addArena($args[1]);
                            break;
                        case "addlevel":
                            if(!$sender->hasPermission("ew.cmd.addlevel")) {
                                $sender->sendMessage("§cYou have not permissions to use this command!");
                                break;
                            }
                            if(empty($args[1])) {
                                $sender->sendMessage(self::$prefix."§7Usage:§c /ew addlevel <level>");
                                break;
                            }
                            if($this->arena->levelExists($args[1])) {
                                $sender->sendMessage(self::$prefix."§cThis level is already added.");
                                break;
                            }
                            if(!$this->getServer()->isLevelGenerated($args[1])) {
                                $sender->sendMessage(self::$prefix."§cLevel {$args[1]} does not exists!");
                                break;
                            }
                            $this->arena->addMap($this->getServer()->getLevelByName($args[1]));
                            $sender->sendMessage(self::$prefix."§aLevel added.");
                            break;
                        case "setlevel":
                            if(!$sender->hasPermission("ew.cmd.setlevel")) {
                                $sender->sendMessage("§cYou have not permissions to use this command!");
                                break;
                            }
                            if(empty($args[1])) {
                                $sender->sendMessage(self::$prefix."§7Usage: §c/ew setlevel <level>");
                                break;
                            }
                            break;
                        case "setlobby":
                            if(!$sender->hasPermission("ew.cmd.setlobby")) {
                                $sender->sendMessage("§cYou have not permissions to use this command!");
                                break;
                            }

                    }
                    break;
            }
        }
    }
}
