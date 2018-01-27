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

namespace eggwars\arena\shop;

use eggwars\arena\Arena;
use eggwars\arena\team\Team;
use eggwars\utils\Color;
use pocketmine\block\Block;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

/**
 * Class ShopManager
 * @package eggwars\arena
 */
class ShopManager {

    /** @var Arena $arena */
    private $arena;

    /** @var array $shopping */
    private $shopping = [];

    /**
     * ShopManager constructor.
     * @param Arena $arena
     */
    public function __construct(Arena $arena) {
        $this->arena = $arena;
    }

    /**
     * @param Player $player
     * @param Team $team
     */
    public function openShop(Player $player, Team $team) {
        $nbt = new CompoundTag('', [
            new StringTag('id', Tile::CHEST),
            new StringTag('CustomName', "§3§lEggWars §7>>> §6Shop"),
            new IntTag('x', $x = intval($player->getX())),
            new IntTag('y', $y = intval($player->getY()) + 4),
            new IntTag('z', $z = intval($player->getZ()))
        ]);
        $inventory = new CustomChestInventory($tile = new Chest($player->getLevel(), $nbt));
        $block = Block::get(Block::CHEST);
        $block->setComponents($x, $y, $z);
        $player->getLevel()->sendBlocks([$player], [$block]);
        $player->addWindow($inventory);
        $this->shopping[$player->getName()] = [$team, 0];
    }

    /**
     * @param Player $player
     * @param Item $item
     */
    public function onBuyTransaction(Player $player, Item $item, int $slot) {
        $inv = $player->getInventory();
        $price = $this->getPrice($item);
        if($inv->contains($this->getPrice($item))) {
            $inv->addItem($item);
            $inv->removeItem($price);
        }
        else {
            $player->sendMessage("§cYou do not have too enough materials!");
        }
    }

    /**
     * @param Player $player
     * @param int $slot
     */
    public function onBrowseTransaction(Player $player, CustomChestInventory $inventory, int $slot) {
        $this->updateChestItems($inventory, $this->getArena()->getTeamByPlayer($player), $slot);
    }

    /**
     * @param Item $item
     * @return Item $item
     */
    public function getPrice(Item $item): Item {
        $price = null;
        while ($price == null) {
            foreach ($this->shopData as $shopIds => $shopItems) {
                foreach ($shopItems as $id => $itemArray) {
                    if(isset($itemArray[5])) {
                        switch ($itemArray[5][0]) {
                            case 0:
                                $price = Item::get(Item::IRON_INGOT, 0, $itemArray[5][1]);
                                break;
                            case 1:
                                $price = Item::get(Item::GOLD_INGOT, 0, $itemArray[5][1]);
                                break;
                            case 2:
                                $price = Item::get(Item::DIAMOND, 0, $itemArray[5][1]);
                                break;
                        }
                    }
                }
            }
        }
        return $price;
    }

    /**
     * @param CustomChestInventory $inventory
     * @param Team $team
     * @param int $id
     */
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
            if(isset($shopItems[$id])) {
                foreach ($shopItems[$id] as $invSlot => $itemArgs) {
                    $inventory->setItem(8+$invSlot, $this->getItemFromArray($itemArgs, $team));
                }
            }
        }
    }

    /**
     * @param Armor $armor
     * @param Team $team
     * @return Armor
     */
    public function setItemTeamColor(Armor $armor, Team $team): Armor {
        $armor->setCustomColor(Color::getColorFromMC($team->getMinecraftColor()));
        return $armor;
    }

    /**
     * @param array $array
     * @return Item
     */
    public function getItemFromArray(array $array, Team $team): Item {
        $item = Item::get(0);
        if(count($array) >= 3) {
            $item = Item::get($array[0], $array[1], $array[2]);
        }
        if(isset($array[4]) && is_array($array[4])) {
            $enchantments = (array)$array[4];
            foreach ($enchantments as $enchantment) {
                if($this->getEnchantmentFromArray($enchantment) instanceof Enchantment) $item->addEnchantment($enchantment);
            }
        }
        if(isset($array[3]) && is_string($array[3])) {
            $item->setCustomName($array[3]);
        }
        // leather armours
        if(in_array($item->getId(), [298, 299, 300, 301]) && $item instanceof Armor) {
            $item = $this->setItemTeamColor($item, $team);
        }
        return $item;
    }

    /**
     * @param array $array
     * @return Enchantment|null $enchantment
     */
    public function getEnchantmentFromArray(array $array) {
        $enchantment = null;
        if(count($array) >= 2) {
            switch ($array[0]) {
                case "protection":
                    $enchantment = new Enchantment(Enchantment::PROTECTION, "Protection", 1, 0, 0);
                    break;
                case "blast_protection":
                    $enchantment = new Enchantment(Enchantment::BLAST_PROTECTION, "Blast Protection", 1, 0, 0);
                    break;
                case "projectile_protection":
                    $enchantment = new Enchantment(Enchantment::PROJECTILE_PROTECTION, "Projectile Protection", 1, 0, 0);
                    break;
                case "feather_falling":
                    $enchantment = new Enchantment(Enchantment::FEATHER_FALLING, "Feather Falling", 1, 0, 0);
                    break;
                case "sharpness":
                    $enchantment = new Enchantment(Enchantment::SHARPNESS, "Sharpness", 1, 0, 0);
                    break;
                case "fire_aspect":
                    $enchantment = new Enchantment(Enchantment::FIRE_ASPECT, "Fire Aspect", 1, 0, 0);
                    break;
                case "knockback":
                    $enchantment = new Enchantment(Enchantment::KNOCKBACK, "Knockback", 1, 0, 0);
                    break;
                case "unbreaking":
                    $enchantment = new Enchantment(Enchantment::UNBREAKING, "Unbreaking", 1, 0, 0);
                    break;
                case "efficiency":
                    $enchantment = new Enchantment(Enchantment::EFFICIENCY, "Efficiency", 1, 0, 0);
                    break;
                case "infinity":
                    $enchantment = new Enchantment(Enchantment::INFINITY, "Infinity", 1, 0, 0);
                    break;
                case "power":
                    $enchantment = new Enchantment(Enchantment::POWER, "Power", 1, 0, 0);
                    break;
                case "punch":
                    $enchantment = new Enchantment(Enchantment::PUNCH, "Punch", 1, 0, 0);
                    break;

            }
            $enchantment->setLevel(intval($array[1]));
        }
        return $enchantment;
    }

    /**
     * @return Arena $arna
     */
    public function getArena(): Arena {
        return $this->arena;
    }

    /**
     * @return array
     */
    public function getBreakableBlocks(): array {
        $blocks = [];
        foreach ($this->shopData as $slot => [$shopSlot => $itemArray]) {
            if(is_int($shopSlot)) {
                $id = $itemArray[0];
                if(Block::get($id) instanceof Block && $id !== 0) {
                    array_push($blocks, $id);
                }
            }
        }
        return $blocks;
    }

    /**
     * @var array $shopData
     */
    private $shopData = [
        0 => [
            "name" => [Item::SANDSTONE, 0, 1, "§6Blocks"],
            0 => [Item::SANDSTONE, 0, 4, "Sandstone", "none", [0, 16]]
        ],
        1 => [
            "name" => [Item::GOLD_SWORD, 0, 1,"§7Swords"],
            0 => [Item::WOODEN_SWORD, 0, 1, "§7Sword lvl1", "none", [0, 10]],
            1 => [Item::STONE_SWORD, 0, 1, "§7Sword lvl2", "none", [1, 12]],
            2 => [Item::GOLD_SWORD, 0, 1, "§7Sword lvl3", "none", [1, 32]],
            3 => [Item::IRON_SWORD, 0, 1, "§7Sword lvl4", "none", [2, 10]],
            4 => [Item::DIAMOND_SWORD, 0, 1, "§7Sword lvl5", "none", [2, 23]],
        ],
        2 => [
            "name" => [Item::IRON_PICKAXE, 0, 1, "§7Pickaxes"],
            0 => [Item::WOODEN_PICKAXE, 0, 1, "§7Pickaxe lvl1", "none", [0, 12]],
            1 => [Item::STONE_PICKAXE, 0, 1, "§7Pickaxe lvl2", "none", [0, 14]]
        ],
        3 => [
            "name" => [Item::STEAK, 0, 1,"§7Food"],
            0 => [Item::APPLE, 0, 5, "Apple", "none", [0, 1]]
        ]
    ];
}