<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\kit\Kit;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class BanditKit extends Kit {

    /**
     * Bandit constructor.
     */
    public function __construct() {
        $name = TextFormat::RESET . TextFormat::BOLD . TextFormat::YELLOW . "Bandit " . TextFormat::RESET;
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::NOURISH), 3),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::QUICKENING), 3),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::HOPS), 1),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::BLEED), 4),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::GUILLOTINE), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHATTER), 4),
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 12),
            Item::get(Item::GOLDEN_APPLE, 0, 48),
        ];
        parent::__construct("Bandit", self::RARE, $items, 432000);
    }
}