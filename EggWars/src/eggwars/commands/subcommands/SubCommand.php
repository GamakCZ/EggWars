<?php

declare(strict_types=1);

namespace eggwars\commands\subcommands;

use eggwars\commands\EggWarsCommand;
use pocketmine\command\CommandSender;

/**
 * Class SubCommand
 * @package eggwars\commands
 */
class SubCommand extends EggWarsCommand {

    /**
     * @var SubCommand[] $subCommands
     */
    private $subCommands = [];

    /**
     * SubCommand constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->registerDefaults();
    }

    private function registerDefaults() {
        $this->registerSubcommand("create", new CreateSubcommand);
    }

    /**
     * @param string $name
     * @param SubCommand $subCommand
     */
    public function registerSubcommand(string $name, SubCommand $subCommand) {
        $this->subCommands[$name] = $subCommand;
    }

    /**
     * @param CommandSender $sender
     * @param string $subcommandName
     * @return bool
     */
    public function checkPermission(CommandSender $sender, string $subcommandName): bool {
        return boolval($sender->hasPermission("ew.cmd.$subcommandName"));
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {}

    /**
     * @param CommandSender $sender
     * @param string $label
     * @param array $args
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $label, array $args) {
        if(isset($args[0]) && isset($args[1])) {
            if(isset($this->subCommands[$args[0]])) {
                $name = $args[0];
                array_shift($args);
                $this->subCommands[$name]->executeSub($sender, $args, $name);
            }
        }
    }
}