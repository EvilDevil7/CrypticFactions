<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\kit\Kit;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class OverlordKit extends Kit {

    /**
     * Overlord constructor.
     */
    public function __construct() {
        $name = "§l§cOver§6lord§r ";
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 11),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::BLEED), 3),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::ANNIHILATION), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::GUILLOTINE), 2),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SHOVEL, $name . "§r§7Shovel§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_PICKAXE, $name . "§r§7Pickaxe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_AXE, $name . "§r§7Axe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 11),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_HOE, $name . "§r§7Hoe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0,  12),
            Item::get(Item::OBSIDIAN, 0, 256),
            Item::get(Item::BEDROCK, 0, 16)
        ];
        parent::__construct("Overlord", self::COMMON, $items, 21600);
    }
}