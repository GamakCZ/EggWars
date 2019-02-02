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

namespace vixikhd\eggwars\arena\shop;

use vixikhd\eggwars\arena\Arena;
use vixikhd\eggwars\arena\team\Team;
use vixikhd\eggwars\event\PlayerOpenShopEvent;
use vixikhd\eggwars\utils\Color;
use pocketmine\block\Block;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
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
    public $shopping = [];

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
        $event = new PlayerOpenShopEvent($player, $this->getArena(), PlayerOpenShopEvent::SHOP_TYPE_CHEST, $this->shopData);
        $this->getArena()->getPlugin()->getServer()->getPluginManager()->callEvent($event);

        if($event->isCancelled() || $event->getShopType() !== PlayerOpenShopEvent::SHOP_TYPE_CHEST) {
            return;
        }

        $this->shopData = $event->getShopItems();

        

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
        $this->updateChestItems($inventory, $this->getArena()->getTeamByPlayer($player), -1);
        $this->shopping[$player->getName()] = [$team, -1];
    }

    /**
     * @param Player $player
     * @param Item $item
     */
    public function onBuyTransaction(Player $player, Item $item, int $slot) {
        $inv = $player->getInventory();
        $price = $this->getPrice($item);
        if($inv->contains($this->getPrice($item))) {
            $item->setCustomName(Item::get($item->getId(), $item->getDamage(), $item->getCount())->getName());
            $player->getInventory()->addItem($item);
            $inv->removeItem($price);
        }
        else {
            $player->sendMessage("§cYou do not have too enough materials!");
        }
    }

    /**
     * @param Player $player
     * @param CustomChestInventory $inventory
     * @param int $slot
     */
    public function onBrowseTransaction(Player $player, CustomChestInventory $inventory, int $slot) {
        $this->updateChestItems($inventory, $this->getArena()->getTeamByPlayer($player), $slot);
    }

    /**
     * @param Item $item
     * @return Item $item
     */
    public function getPrice(Item $item): Item{
        $price = null;
        $shopData = null;
        foreach ($this->shopData as $arrays) {
            foreach ($arrays as $index => $shopItems) {
                if(!is_string($index)) {
                    if($shopItems[0] == $item->getId() && $shopItems[1] == $item->getDamage() && $shopItems[2] == $item->getCount()/* && $shopItems[3] == $item->getName()*/) {
                        $priceItem = $shopItems[5];
                        $id = null;
                        if($priceItem[0] == 0) {
                            $id = Item::IRON_INGOT;
                        }
                        elseif($priceItem[0] == 1) {
                            $id = Item::GOLD_INGOT;
                        }
                        else {
                            $id = Item::DIAMOND;
                        }
                        $price = Item::get($id, 0, $priceItem[1]);
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
            for($x = 0; $x <= 8; $x++) {
                if(isset($shopItems[$x])) {
                    $itemArray = $shopItems[$x]["name"];
                    $inventory->setItem($x, Item::get($itemArray[0], $itemArray[1], $itemArray[2])->setCustomName($itemArray[3]));
                }
            }
        }
        else {
            for($x = 8; $x <= 26; $x++) {
                $inventory->setItem($x, Item::get(0));
            }

            if(isset($shopItems[$id])) {
                foreach ($shopItems[$id] as $invSlot => $itemArgs) {
                    if(is_int($invSlot)) {
                        $inventory->setItem($invSlot+9, $this->getItemFromArray($itemArgs, $team));
                    }
                }
            }
        }
    }

    /**
     * @param Armor $armor
     * @param Team $team
     *
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
                if(($ench = $this->getEnchantmentFromArray($enchantment)) instanceof Enchantment) $item->addEnchantment($ench);
            }
        }
        $priceItem = null;

        if($array[5][0] == 0) {
            $priceItem = Item::get(Item::IRON_INGOT)->setCustomName("§7Iron");
        }
        elseif($array[5][0] == 1) {
            $priceItem = Item::get(Item::GOLD_INGOT)->setCustomName("§6Gold");
        }
        else {
            $priceItem = Item::get(Item::DIAMOND)->setCustomName("§bDiamond");
        }

        $priceItem->setCount(intval($array[5][1]));

        if(isset($array[3]) && is_string($array[3])) {
            $item->setCustomName($array[3]."\n"."§9{$priceItem->getName()} §3x{$priceItem->getCount()}");
        }
        // leather armours
        if(in_array($item->getId(), [298, 299, 300, 301]) && $item instanceof Armor) {
            $item = $this->setItemTeamColor($item, $team);
        }
        return $item;
    }

    /**
     * @param array $array
     * @return Enchantment|EnchantmentInstance|null $enchantment
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

            if(class_exists(EnchantmentInstance::class)) {
                $enchantment = new EnchantmentInstance($enchantment, intval($array[1]));
            }
            else {
                $enchantment->setLevel(intval($array[1]));
            }

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
        return [Item::SANDSTONE, Item::OBSIDIAN, Item::IRON_BLOCK, Item::CHEST, Item::END_STONE];
    }

    /**
     * @var array $shopData
     */
    private $shopData = [
        0 => [
            "name" => [Item::SANDSTONE, 0, 1, "§6Blocks"],
            0 => [Item::SANDSTONE, 0, 4, "Sandstone", "none", [0, 2]],
            1 => [Item::SANDSTONE, 0, 32, "Sandstone", "none", [0, 16]],
            2 => [Item::END_STONE, 0, 1, "End Stone", "none", [0, 4]],
            3 => [Item::IRON_BLOCK, 0, 1, "Iron Block", "none", [0, 16]],
            4 => [Item::OBSIDIAN, 0, 1, "Obsidian", "none", [1, 25]],
            5 => [Item::CHEST, 0, 1, "Chest", "none", [0, 20]]
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
            0 => [Item::WOODEN_PICKAXE, 0, 1, "§7 Pickaxe lvl1", "none", [0, 12]],
            1 => [Item::STONE_PICKAXE, 0, 1, "§7Pickaxe lvl2", "none", [0, 14]],
            2 => [Item::IRON_PICKAXE, 0, 1, "§7Pickaxe lvl3", [["efficiency", 1]], [1, 20]],
            3 => [Item::DIAMOND_PICKAXE, 0, 1, "§7Pickaxe lvl4", [["efficiency", 4]], [2, 20]],
            4 => [Item::WOODEN_AXE, 0, 1, "Axe", "none", [2, 1]]
        ],
        3 => [
            "name" => [Item::STEAK, 0, 1,"§7Food"],
            0 => [Item::APPLE, 0, 5, "Apple", "none", [0, 1]],
            1 => [Item::STEAK, 0, 4, "Steak", "none", [0, 10]],
            2 => [Item::CAKE, 0, 1, "Cake", "none", [1, 5]],
            3 => [Item::GOLDEN_APPLE, 0, 1 ,"Golden Apple", "none", [2, 10]]
        ],
        4 => [
            "name" => [Item::DIAMOND_CHESTPLATE, 0, 1, "§7Armours"],
            0 => [Item::LEATHER_CAP, 0, 1, "Leather Cap", "none", [0, 5]],
            1 => [Item::LEATHER_TUNIC, 0, 1, "Leather Tunic", "none", [0, 5]],
            2 => [Item::LEATHER_LEGGINGS, 0, 1, "Leather Leggings", "none", [0, 5]],
            3 => [Item::LEATHER_BOOTS, 0, 1, "Leather Boots", "none", [0, 5]],
            4 => [Item::CHAIN_HELMET, 0, 1, "Chain Helmet", "none", [1, 20]],
            5 => [Item::CHAIN_CHESTPLATE, 0, 1, "Chain Chestplate", "none", [1, 20]],
            6 => [Item::CHAIN_LEGGINGS, 0, 1, "Chain Leggings", "none", [1, 20]],
            7 => [Item::CHAIN_BOOTS, 0, 1, "Chain Helmet", "none", [1, 20]],
            8 => [Item::IRON_HELMET, 0, 1, "Iron Boots", "none", [2, 20]],
            9 => [Item::IRON_CHESTPLATE, 0, 1, "Iron Leggings", "none", [2, 20]],
            10 => [Item::IRON_LEGGINGS, 0, 1, "Iron Chestplate", "none", [2, 20]],
            11 => [Item::IRON_BOOTS, 0, 1, "Iron Helmet", "none", [2, 20]],
        ],
        5 => [
            "name" => [Item::BOW, 0, 1, "§7Bows"],
            0 => [Item::BOW, 0, 1, "Bow", "none", [2, 5]],
            1 => [Item::BOW, 0, 1, "Bow lvl1", [["power", 1]], [2, 10]],
            2 => [Item::BOW, 0, 1, "Bow lvl2", [["power", 3], ["punch", 1]], [2, 20]],
            3 => [Item::ARROW, 0, 8, "Arrow", "none", [0, 10]]
        ],
        6 => [
            "name" => [Item::SPONGE, 0, 1, "§8§k|||§r §6Special§8 §k|||§r"],
            0 => [Item::ENDER_PEARL, 0, 1, "§3EnderPearl", "none", [2, 15]],
            1 => [Item::SPONGE, 0, 1, "§eLucky§6Block", "none", [2, 1]]
        ]
    ];
}