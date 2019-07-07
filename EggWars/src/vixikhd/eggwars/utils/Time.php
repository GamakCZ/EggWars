<?php

/*
 *    _____                  __        __
 *   | ____|   __ _    __ _  \ \      / /   __ _   _ __   ___
 *   |  _|    / _` |  / _` |  \ \ /\ / /   / _` | | '__| / __|
 *   | |___  | (_| | | (_| |   \ V  V /   | (_| | | |    \__ \
 *   |_____|  \__, |  \__, |    \_/\_/     \__,_| |_|    |___/
 *           |___/   |___/
 */

declare(strict_types=1);

namespace vixikhd\eggwars\utils;

/**
 * Class Time
 * @package eggwars\utils
 */
class Time {

    /**
     * @param int $time
     * @return string
     */
    public static function calculateTime(int $time): string {
        return gmdate("i:s", $time);
    }
}