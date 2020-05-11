<?php

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\CrypticPlayer;
use core\translation\Translation;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class BleedEnchantment extends Enchantment {

    /**
     * BleedEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::BLEED, "Bleed", self::RARITY_MYTHIC, "Have a chance to multiply your damage.", self::DAMAGE, self::SLOT_SWORD, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$entity instanceof CrypticPlayer) or (!$damager instanceof CrypticPlayer)) {
                return;
            }
            $random = mt_rand(1, 250);
            $chance = $level * 3;
            if($chance >= $random) {
                $event->setBaseDamage($event->getBaseDamage() * 1.5);
                $entity->sendMessage(Translation::ORANGE . "You are bleeding.");
                $damager->sendMessage(Translation::ORANGE . "Your opponent is bleeding.");
            }
        };
    }
}