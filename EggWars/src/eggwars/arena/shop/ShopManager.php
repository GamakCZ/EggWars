<?php

declare(strict_types=1);

namespace eggwars\arena;

use eggwars\arena\shop\CustomChestInventory;
use eggwars\arena\team\Team;
use pocketmine\block\Block;
use pocketmine\inventory\ChestInventory;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

/**
 * Class ShopManager
 * @package eggwars\arena
 */
class ShopManager {

    /**
     * @var Arena $arena
     */
    private $arena;

    private $shopping = [];

    /**
     * ShopManager constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    public function openShop(Player $player, Team $team) {
        $inventory = new CustomChestInventory($tile = new Chest($player->getLevel(), Chest::createNBT($tileVec = new Vector3(intval($player->getX()), intval($player->getY())+4, intval($player->getZ())))));
        $block = Block::get(Block::CHEST);
        $block->setComponents($tileVec->getX(), $tileVec->getY(), $tileVec->getZ());
        $player->getLevel()->sendBlocks([$player], [$block]);
        $player->addWindow($inventory);
        $this->shopping[$player->getName()] = [$team, 0];
    }

    public function updateChestItems(CustomChestInventory $inventory, Team $team, int $id) {
        $shopItems = $this->shopData;
        if($id == -1) {
            foreach ($shopItems as $slot => ["name" => $data]) {
                if($slot <= 8) {
                    $inventory->setItem($slot, Item::get($data[0], $data[1], $data[2])->setCustomName($data[3]));
                }
            }
        }
        else {
            
        }
    }

    /**
     * @return Arena $arna
     */
    public function getArena(): Arena {
        return $this->arena;
    }

    /**
     * @var array $shopData
     */
    private $shopData = [
        0 => [
            "name" => [Item::GOLD_SWORD, 0, 1,"§7Swords"],
            0 => [Item::WOODEN_SWORD, 0, 1, "§7Sword lvl1", "none", [0, 10]],
            1 => [Item::STONE_SWORD, 0, 1, "§7Sword lvl2", "none", [1, 12]],
            2 => [Item::GOLD_SWORD, 0, 1, "§7Sword lvl3", "none", [1, 32]],
            3 => [Item::IRON_SWORD, 0, 1, "§7Sword lvl4", "none", [2, 10]],
            4 => [Item::DIAMOND_SWORD, 0, 1, "§7Sword lvl5", "none", [2, 23]],
        ],
        1 => [
            "name" => [Item::IRON_PICKAXE, 0, 1, "§7Pickaxes"],
            0 => [Item::WOODEN_PICKAXE, 0, 1, "§7Pickaxe lvl1", "none", [0, 12]],
            1 => [Item::STONE_PICKAXE, 0, 1, "§7Pickaxe lvl2", "none", [0, 14]]
        ],
        2 => [
            "name" => [Item::STEAK, 0, 1,"§7Food"],
            0 => [Item::APPLE, 0, 5, "Apple", "none", [0, 1]]
        ]
    ];
}