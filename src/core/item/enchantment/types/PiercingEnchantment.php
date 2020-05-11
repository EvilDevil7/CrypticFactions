<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class PiercingEnchantment extends Enchantment {

    /**
     * PiercingEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::PIERCING, "Piercing", self::RARITY_RARE, "Have a chance to ignore most armor protection and deal more damage.", self::DAMAGE, self::SLOT_BOW, 3);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$entity instanceof Player) or (!$damager instanceof Player)) {
                return;
            }
            if($event->getCause() !== EntityDamageByEntityEvent::CAUSE_PROJECTILE) {
                return;
            }
            $random = mt_rand(1, 150);
            $chance = $level * 3;
            if($chance >= $random) {
               $event->setBaseDamage($event->getBaseDamage() * (0.8 + ($level / 5)));
            }
        };
    }
}