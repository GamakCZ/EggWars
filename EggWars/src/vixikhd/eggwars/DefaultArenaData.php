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

namespace vixikhd\eggwars;

/**
 * Interface DefaultArenaData
 * @package vixikhd\eggwars
 */
interface DefaultArenaData {

    public const DEFAULT_ARENA_DATA = [
        "enabled" => false,
        "name" => "",
        "startTime" => 30,
        "gameTime" => 3600,
        "restartTime" => 20,
        "teamsToStart" => 2,
        "playersPerTeam" => 2,
        "lobby" => [0, 98, 0, "world"],
        "sign" => [0, 100, 0, "world"],
        "builder" => "VixikCZ",
        "teamsCount" => 2,
        "teams" => []
    ];
}