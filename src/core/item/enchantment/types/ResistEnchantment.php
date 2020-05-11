<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class ResistEnchantment extends Enchantment {

    /**
     * ResistEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::RESIST, "Resist", self::RARITY_RARE, "Reduce knockback effects.", self::DAMAGE_BY, self::SLOT_ARMOR, 3);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) {
                return;
            }
            $event->setKnockBack($event->getKnockBack() / (1 + (0.1 * $level)));
        };
    }
}