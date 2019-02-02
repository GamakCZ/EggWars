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

/**
 * Interface LevelManager
 * @package vixikhd\eggwars\arena\level
 */
interface LevelManager {

    /**
     * @param Arena $arena
     * @return bool
     */
    public function init(Arena $arena): bool;

    /**
     * @return array $levelData
     */
    public function getLevelData(): array;

    /**
     * @return Level $level
     */
    public function getLevel(): Level;

    /**
     * @return void
     *
     * called 5 sec between start
     */
    public function chooseMap();
}