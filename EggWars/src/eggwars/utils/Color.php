<?php

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
     * @return string
     */
    public static function getMCFromId(string $id) {
        $mc = null;
        foreach (self::ALL as $colors => [$mcFormat, $nameFormat, $idFormat, $htmlArray]) {
            if($id == $idFormat) {
                $mc = $mcFormat;
            }
        }
        return $mc;
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
        $html = self::getColorFromMC($mc);
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
}