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

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\EggWars;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;

/**
 * Class TeamCommand
 * @package eggwars\commands
 */
class TeamCommand extends Command implements PluginIdentifiableCommand {

    /**
     * TeamCommand constructor.
     */
    public function __construct() {
        parent::__construct("team", "Select your team", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return false;
        }
        if(empty($args[0])) {
            $sender->sendMessage("§cUsage: §7/team <team>");
            return false;
        }
        $arena = $this->getPlugin()->getArenaManager()->getArenaByPlayer($sender);
        if(!$arena instanceof Arena) {
            $sender->sendMessage("§cJoin EggWars game to use this command!");
            return false;
        }
        if($arena->teamExists($args[0])) {
            $arena->addPlayerToTeam($sender, $args[0]);
        }
        else {
            $sender->sendMessage(EggWars::getPrefix()."§7Team $args[0] does not found!");
        }
        return false;
    }

    /**
     * @return EggWars|Plugin $plugin
     */
    public function getPlugin(): Plugin {
        return EggWars::getInstance();
    }
}