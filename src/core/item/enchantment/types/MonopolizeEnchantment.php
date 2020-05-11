<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\CrypticPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class MonopolizeEnchantment extends Enchantment {

    /**
     * MonopolizeEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::MONOPOLIZE, "Monopolize", self::RARITY_MYTHIC, "Have a chance to steal xp from your opponent.", self::DAMAGE, self::SLOT_SWORD, 5);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $damager = $event->getDamager();
            $entity = $event->getEntity();
            if((!$entity instanceof CrypticPlayer) or (!$damager instanceof CrypticPlayer)) {
                return;
            }
            if($entity->getCurrentTotalXp() === 0) {
                return;
            }
            if(mt_rand(1, 5) === mt_rand(1, 5)) {
                if($entity->getCurrentTotalXp() < ($level * 2)) {
                    $amount = $entity->getCurrentTotalXp();
                }
                else {
                    $amount = $level * 2;
                }
                $entity->subtractXp($amount);
                $damager->addXp($amount);
            }
        };
    }
}