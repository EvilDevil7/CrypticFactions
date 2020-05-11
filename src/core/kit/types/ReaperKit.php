<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\kit\Kit;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class ReaperKit extends Kit {

    /**
     * Overlord constructor.
     */
    public function __construct() {
        $name = TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_PURPLE . "Reaper " . TextFormat::RESET;
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::QUICKENING), 2),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 12),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::BLEED), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::ANNIHILATION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::GUILLOTINE), 8),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::WITHER), 3),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHATTER), 2),
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 20),
            Item::get(Item::GOLDEN_APPLE, 0, 48),
        ];
        parent::__construct("Reaper", self::MYTHIC, $items, 432000);
    }
}