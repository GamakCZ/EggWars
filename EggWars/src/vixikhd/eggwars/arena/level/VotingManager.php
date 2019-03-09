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

namespace vixikhd\eggwars\arena\level;

use pocketmine\level\Level;
use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\arena\level\LevelManager;

/**
 * Class VoteManager
 * @package eggwars\arena\voting
 */
class VotingManager implements LevelManager {

    /** @var Level $level */
    public $level;

    public function init(Arena $arena): bool {

    }

    public function getLevel(): Level {

    }

    public function chooseMap() {
    }

    public function getLevelData(): array {

    }
}
