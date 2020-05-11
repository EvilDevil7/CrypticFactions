<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\kit\Kit;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class AssassinKit extends Kit {

    /**
     * Assassin constructor.
     */
    public function __construct() {
        $name = "§l§cAssassin§r ";
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 9),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EVADE), 5),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 9),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EVADE), 5),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 9),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EVADE), 5),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 9),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::IMMUNITY), 1),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EVADE), 5),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::QUICKENING), 1),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 12),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::BLEED), 8),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::ANNIHILATION), 2),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::GUILLOTINE), 10),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SLAUGHTER), 5),
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 10),
            Item::get(Item::GOLDEN_APPLE, 0, 48),
        ];
        parent::__construct("Assassin", self::RARE, $items, 432000);
    }
}
