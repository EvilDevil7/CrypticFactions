<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\kit\Kit;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class ArcherKit extends Kit {

    /**
     * Archer constructor.
     */
    public function __construct() {
        $name = "§l§3Archer§r ";
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::QUICKENING), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::HOPS), 1),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 9),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10)
            ]))->getItemForm(),
            (new CustomItem(Item::BOW, $name . "§r§7Bow§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::POWER), 6),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::VELOCITY), 4),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PARALYZE), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PIERCING), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10)
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 12),
            Item::get(Item::GOLDEN_APPLE, 0, 48),
            Item::get(Item::ARROW, 0, 256),
        ];
        parent::__construct("Archer", self::LEGENDARY, $items, 432000);
    }
}