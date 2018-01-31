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
use pocketmine\item\Item;

/**
 * Class Color
 * @package eggwars\utils
 *
 * MC $mc = Minecraft Color
 */
class Color implements ColorIds {

    /**
     * @param string $mc
     * @return string
     */
    public static function getColorNameFormMC(string $mc): string  {
        $name = "";
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $id, $htmlArray]) {
            if($mc == $mcFormat) {
                $name = $nameFormat;
            }
        }
        return $name;
    }

    /**
     * @param string $id
     * @param int $color
     * @return mixed
     */
    public static function getMCFromId(string $id, int $color = 0) {
        $mc = [];
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $idFormat, $htmlArray]) {
            if($id == $idFormat) {
                array_push($mc, $mcFormat);
            }
        }
        return $mc[$color];
    }

    /**
     * @param string $mc
     * @return Item $item
     */
    public static function getWoolFormMC(string $mc) {
        /** @var Item $item */
        $item = null;
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $id, $htmlArray]) {
            if($mc == $mcFormat) {
                $item = Item::fromString($id);
            }
        }
        return $item;
    }

    /**
     * @param string $mc
     * @return \pocketmine\utils\Color
     */
    public static function getColorFromMC(string $mc): \pocketmine\utils\Color {
        $html = [];
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $idFormat, $htmlArray]) {
            if($mc == $mcFormat) $html = $htmlArray;
        }
        return new \pocketmine\utils\Color($html[0], $html[1], $html[2]);
    }

    /**
     * @param string $mc
     * @return array $html
     */
    public static function getHtmlFromMC(string $mc): array {
        $html = [];
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $id, $htmlArray]) {
            if($mc == $mcFormat) {
                $html = $htmlArray;
            }
        }
        return $html;
    }

    /**
     * @param string $mc
     * @return bool
     */
    public static function mcColorExists(string $mc): bool {
        $exists = false;
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $id, $htmlArray]) {
            if($mc == $mcFormat) $exists = true;
        }
        return $exists;
    }
}