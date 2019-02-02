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
        $min = (int)$time/60;
        if(!is_int($min)) {
            $min = intval($min);
        }
        $min = strval($min);
        if(strlen($min) == 0) {
            $min = "00";
        }
        elseif(strlen($min) == 1) {
            $min = "0{$min}";
        }
        else {
            $min = strval($min);
        }
        $sec = $time%60;
        if(!is_int($sec)) {
            $sec = intval($sec);
        }
        $sec = strval($sec);
        if(strlen($sec) == 0) {
            $sec = "00";
        }
        elseif(strlen($sec) == 1) {
            $sec = "0{$sec}";
        }
        else {
            $sec = strval($sec);
        }
        if($time <= 0) {
            return "00:00";
        }
        return strval($min.":".$sec);
    }
}