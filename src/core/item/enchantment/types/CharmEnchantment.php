<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\block\BlockBreakEvent;

class CharmEnchantment extends Enchantment {

    /**
     * CharmEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::CHARM, "Charm", self::RARITY_UNCOMMON, "Increase your chance of getting a lucky reward by mining a lucky block.", self::BREAK, self::SLOT_DIG, 10);
        $this->callable = function(BlockBreakEvent $event, int $level) {
        };
    }
}