<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\item\types\EnchantmentRemover;
use core\item\types\XPNote;
use core\kit\Kit;

class EnchanterKit extends Kit {

    /**
     * Enchanter constructor.
     */
    public function __construct() {
        $items =  [
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new EnchantmentRemover(100))->getItemForm(),
            (new EnchantmentRemover(100))->getItemForm(),
            (new XPNote(100000))->getItemForm()
        ];
        parent::__construct("Enchanter", self::MYTHIC, $items, 432000);
    }
}