<?php

declare(strict_types=1);

namespace eggwars\commands;

use eggwars\commands\subcommands\ArenasSubcommand;
use eggwars\commands\subcommands\CreateSubcommand;
use eggwars\commands\subcommands\DeleteSubcommand;
use eggwars\commands\subcommands\HelpSubcommand;
use eggwars\commands\subcommands\SetSubcommand;
use eggwars\commands\subcommands\SubCommand;
use eggwars\EggWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

/**
 * Class EggWarsCommand
 * @package eggwars\commands
 */
class EggWarsCommand extends Command implements PluginIdentifiableCommand {

    /** @var SubCommand[] */
    public $subCommands = [];

    /**
     * EggWarsCommand constructor.
     */
    public function __construct() {
        parent::__construct("eggwars", "EggWars commands", null, ["ew"]);
        $this->registerSub("help", new HelpSubcommand);
        $this->registerSub("create", new CreateSubcommand);
        $this->registerSub("arenas", new ArenasSubcommand);
        $this->registerSub("set", new SetSubcommand);
        $this->registerSub("delete", new DeleteSubcommand);
    }

    /**
     * @param $name
     * @param SubCommand $sub
     */
    public function registerSub($name, SubCommand $sub) {
        $this->subCommands[$name] = $sub;
    }

    /**
     * @param CommandSender $sender
     * @param string $subcommandName
     * @return bool
     */
    public function checkPermission(CommandSender $sender, string $subcommandName) {
        return $sender->hasPermission("ew.cmd.$subcommandName");
    }

    #public function executeSub(CommandSender $sender, array $args, string $name) {}

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(empty($args[0])) {
            if($sender->hasPermission("ew.cmd.help")) {
                $sender->sendMessage("§cUsage: §7/ew help");
            }
            else {
                $sender->sendMessage("§cUsage: §7/ew join §8| §7/ew leave");
            }
            return;
        }
        $name = $args[0];
        if(empty($this->subCommands[$name])) {
            $sender->sendMessage("§cUsage: §7/ew help");
            return;
        }
        array_shift($args);
        $this->subCommands[$name]->executeSub($sender, $args, $name);
    }

    /**
     * @return Plugin|EggWars $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}