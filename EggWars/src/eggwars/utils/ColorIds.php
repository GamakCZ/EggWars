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

namespace eggwars\utils;

/**
 * Interface ColorIds
 * @package eggwars\utils
 */
interface ColorIds {
    const WHITE = ["§f", "White", "35:0", [255, 255, 255]];
    const ORANGE = ["§6", "Orange", "35:1", [255, 230, 0]];
    const MAGENTA = ["§d", "Magenta", "35:2", 255, 0, 255];
    const LIGHT_BLUE = ["§b", "Light Blue", "35:3", [0, 255, 255]];
    const YELLOW = ["§e", "Yellow", "35:4", [255, 255, 0]];
    const LIME = ["§a", "Lime", "35:5", [180, 225, 0]];
    const PINK = ["§d", "Pink", "35:6", [255, 0, 220]];
    const GRAY = ["§8", "Gray", "35:7", [120, 90, 90]];
    const LIGHT_GRAY = ["§7", "Light Gray", "35:8", [196, 196, 196]];
    const CYAN = ["§3", "Cyan", "35:9", [80, 190, 170]];
    const PURPLE = ["§5", "Purple", "35:10", [220, 0, 255]];
    const BLUE = ["§1", "Blue", "35:11", [0, 0, 225]];
    const BROWN = ["§6", "Brown", "35:12", [120, 110, 0]];
    const GREEN = ["§2", "Green", "35:13", [0, 225, 0]];
    const RED = ["§4", "Red", "35:14", [225, 0, 0]];
    const LIGHT_RED = ["§c", "Red", "35:14", [225, 0, 0]];
    const BLACK = ["§0", "Black", "35:15", [0, 0, 0]];

    const ALL = [self::WHITE,
        self::ORANGE,
        self::MAGENTA,
        self::LIGHT_BLUE,
        self::YELLOW,
        self::LIME,
        self::PINK,
        self::GRAY,
        self::LIGHT_GRAY,
        self::CYAN,
        self::PURPLE,
        self::BLUE,
        self::BROWN,
        self::GREEN,
        self::RED,
        self::BLACK];
}