<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\block\BlockBreakEvent;

class AmplifyEnchantment extends Enchantment {

    /**
     * AmplifyEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::AMPLIFY, "Amplify", self::RARITY_RARE, "Increase the amount of xp received by mining.", self::BREAK, self::SLOT_DIG, 5);
        $this->callable = function(BlockBreakEvent $event, int $level) {
            $event->setXpDropAmount((int)round($event->getXpDropAmount() * (1 + ($level * 0.5))));
        };
    }
}