<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityShootBowEvent;

class VelocityEnchantment extends Enchantment {

    /**
     * VelocityEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::VELOCITY, "Velocity", self::RARITY_RARE, "Increase the speed of your arrow and make it travel straighter.", self::SHOOT, self::SLOT_BOW, 5);
        $this->callable = function(EntityShootBowEvent $event, int $level) {
            $event->setForce($event->getForce() + (1 + $level));
            return;
        };
    }
}