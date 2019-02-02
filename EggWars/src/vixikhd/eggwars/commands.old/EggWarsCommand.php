<?php

/**
 *    Copyright 2018-2019 GamakCZ
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace vixikhd\eggwars\commands;

use vixikhd\eggwars\commands\subcommands\ArenasSubcommand;
use vixikhd\eggwars\commands\subcommands\CreateSubcommand;
use vixikhd\eggwars\commands\subcommands\DeleteSubcommand;
use vixikhd\eggwars\commands\subcommands\HelpSubcommand;
use vixikhd\eggwars\commands\subcommands\LeaveSubcommand;
use vixikhd\eggwars\commands\subcommands\LevelSubcommand;
use vixikhd\eggwars\commands\subcommands\SetSubcommand;
use vixikhd\eggwars\commands\subcommands\ShopSubcommand;
use vixikhd\eggwars\commands\subcommands\StartSubcommand;
use vixikhd\eggwars\commands\subcommands\SubCommand;
use vixikhd\eggwars\EggWars;
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
        $this->registerSubcommands();
    }

    private function registerSubcommands() {
        $this->registerSub("help", new HelpSubcommand);
        $this->registerSub("create", new CreateSubcommand);
        $this->registerSub("arenas", new ArenasSubcommand);
        $this->registerSub("set", new SetSubcommand);
        $this->registerSub("delete", new DeleteSubcommand);
        $this->registerSub("level", new LevelSubcommand);
        $this->registerSub("leave", new LeaveSubcommand);
        $this->registerSub("start", new StartSubcommand);
        $this->registerSub("shop", new ShopSubcommand);
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


    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
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