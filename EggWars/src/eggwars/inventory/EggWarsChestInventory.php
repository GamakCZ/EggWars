<?php

declare(strict_types=1);

namespace eggwars\inventory;

use pocketmine\block\Block;
use pocketmine\inventory\ChestInventory;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\tile\Chest;

/**
 * Class EggWarsChestInventory
 * @package eggwars\inventory
 */
class EggWarsChestInventory {

    /**
     * @param Player $player
     * @return ChestInventory
     */
    public static function spawn(Player $player): ChestInventory {
        $inv = new ChestInventory($tile = new Chest($player->getLevel(), Chest::createNBT($pos = $player->add(0, 5, 0))));
        $block = Block::get(Block::CHEST);
        $block->setComponents($pos->x, $pos->y, $pos->z);
        $player->getLevel()->sendBlocks([$player], [$block], UpdateBlockPacket::FLAG_NONE, false);
        return $inv;
    }

    public static function despawn(Player $player, ChestInventory $inventory) {
        $player->getLevel()->sendBlocks([$player], [Block::get(0)]);
        $tile = $inventory->getHolder();
        $tile->close();
    }
}