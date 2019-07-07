<?php

declare(strict_types=1);

namespace vixikhd\eggwars\arena;

/**
 * Interface IngotGeneratorData
 * @package vixikhd\eggwars\arena
 */
interface IngotGeneratorData {

    public const INGOT_IRON = [
        "maxLevel" => 5,
        "delay" => [
            1 => 4,
            2 => 2,
            3 => 1,
            4 => 0.5,
            5 => 0.25
        ]
    ];

    public const INGOT_GOLD = [
        "maxLevel" => 4,
        "delay" => [
            1 => 15,
            2 => 12,
            3 => 8,
            4 => 4
        ]
    ];

    public const INGOT_DIAMOND = [
        "maxLevel" => 4,
        "delay" => [
            1 => 60,
            2 => 45,
            3 => 25,
            4 => 15
        ]
    ];
}