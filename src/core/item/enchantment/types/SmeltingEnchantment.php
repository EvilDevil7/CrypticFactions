<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Item;

class SmeltingEnchantment extends Enchantment {

    const ITEMS = [
        Item::COBBLESTONE => Item::STONE,
        Item::IRON_ORE => Item::IRON_INGOT,
        Item::GOLD_ORE => Item::GOLD_INGOT,
        Item::SAND => Item::GLASS,
        Item::CLAY => Item::BRICK,
    ];

    /**
     * SmeltingEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::SMELTING, "Smelting", self::RARITY_RARE, "Automatically turn a ore that you mine into their mineral forms.", self::BREAK, self::SLOT_DIG, 1);
        $this->callable = function(BlockBreakEvent $event, int $level) {
            $block = $event->getBlock();
            $player = $event->getPlayer();
            if(!isset(self::ITEMS[$block->getId()])) {
                return;
            }
            $player->getInventory()->removeItem(Item::get($block->getId(), 0, 1));
            $player->getInventory()->addItem(Item::get(self::ITEMS[$block->getId()], 0, 1));
        };
    }
}