<?php

declare(strict_types=1);

namespace eggwars\utils;

/**
 * Interface Color
 * @package eggwars\utils
 */
interface Color {
    const WHITE = ["§f", "White", "35:0"];
    const ORANGE = ["§6", "Orange", "35:1"];
    const MAGENTA = ["§d", "Magenta", "35:2"];
    const LIGHT_BLUE = ["§b", "Light Blue", "35:3"];
    const YELLOW = ["§e", "Yellow", "35:4"];
    const LIME = ["§a", "Lime", "35:5"];
    const PINK = ["§d", "Pink", "35:6"];
    const GRAY = ["§8", "Gray", "35:7"];
    const LIGHT_GRAY = ["§7", "Light Gray", "35:8"];
    const CYAN = ["§3", "Cyan", "35:9"];
    const PURPLE = ["§5", "Purple", "35:10"];
    const BLUE = ["§1", "Blue", "35:11"];
    const BROWN = ["§6", "Brown", "35:12"];
    const GREEN = ["§2", "Green", "35:13"];
    const RED = ["§4", "Red", "35:14"];
    const BLACK = ["§0", "Black", "35:15"];

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