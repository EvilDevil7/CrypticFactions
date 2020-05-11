<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\CustomItem;
use core\kit\Kit;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class GodKit extends Kit {

    /**
     * God constructor.
     */
    public function __construct() {
        $name = "§l§cG§3o§cd§r ";
        $items =  [
            (new CustomItem(Item::DIAMOND_HELMET, $name . "§r§7Helmet§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_CHESTPLATE, $name . "§r§7Chestplate§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_LEGGINGS, $name . "§r§7Leggings§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_BOOTS, $name . "§r§7Boots§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::PROTECTION), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SWORD, $name . "§r§7Sword§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 8),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5),
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_SHOVEL, $name . "§r§7Shovel§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_PICKAXE, $name . "§r§7Pickaxe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_AXE, $name . "§r§7Axe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::SHARPNESS), 8),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            (new CustomItem(Item::DIAMOND_HOE, $name . "§r§7Hoe§r", [], [
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 7),
                new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 5)
            ]))->getItemForm(),
            Item::get(Item::STEAK, 0, 64),
            Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 5),
            Item::get(Item::OBSIDIAN, 0, 128),
            Item::get(Item::BEDROCK, 0, 4)
        ];
        parent::__construct("God", self::COMMON, $items, 21600);
    }
}