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

namespace vixikhd\eggwars\commands\subcommands;

use vixikhd\eggwars\commands\EggWarsCommand;
use vixikhd\eggwars\EggWars;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class ListSubcommand
 * @package eggwars\commands\subcommands
 */
class ArenasSubcommand extends EggWarsCommand implements SubCommand {

    /**
     * ArenasSubcommand constructor.
     */
    public function __construct(){}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if(!$this->checkPermission($sender, $name)) return;
        if(!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can be used only in-game!");
            return;
        }
        $sender->sendMessage(EggWars::getPrefix().$this->getPlugin()->getArenaManager()->getListArenasInString());
    }
}