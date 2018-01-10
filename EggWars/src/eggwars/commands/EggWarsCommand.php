<?php

declare(strict_types=1);

namespace eggwars\commands;

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

    /** @var SubCommand $subCommand */
    private $subCommand;

    /**
     * EggWarsCommand constructor.
     */
    public function __construct() {
        parent::__construct("eggwars", "EggWars commands", null, ["ew"]);
    }

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
    }

    /**
     * @return Plugin|EggWars $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}