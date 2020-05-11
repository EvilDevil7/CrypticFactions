<?php

namespace core\item\enchantment\types;

use core\combat\boss\ArtificialIntelligence;
use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class SlaughterEnchantment extends Enchantment {

    /**
     * SlaughterEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::SLAUGHTER, "Slaughter", self::RARITY_MYTHIC, "Deal more damage to bosses.", self::DAMAGE, self::SLOT_SWORD, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof ArtificialIntelligence) {
                return;
            }
            $damage = $event->getBaseDamage();
            $damage = $damage + ($level * 1.5);
            $event->setBaseDamage($damage);
        };
    }
}