<?php

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class AnnihilationEnchantment extends Enchantment {

    /**
     * AnnihilationEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::ANNIHILATION, "Annihilation", self::RARITY_MYTHIC, "Increase damage the lower your opponent's health is.", self::DAMAGE, self::SLOT_SWORD, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Living) {
                return;
            }
            if($entity->getHealth() < 17) {
                $event->setBaseDamage($event->getBaseDamage() + ((($level / 9) * ($entity->getMaxHealth() - $entity->getHealth())) - 2));
            }
        };
    }
}