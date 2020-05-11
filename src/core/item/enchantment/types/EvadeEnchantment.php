<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\translation\Translation;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class EvadeEnchantment extends Enchantment {

    /**
     * EvadeEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::EVADE, "Evade", self::RARITY_MYTHIC, "Have a dodge an attack.", self::DAMAGE_BY, self::SLOT_ARMOR, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) {
                return;
            }
            $random = mt_rand(1, 600);
            $chance = $level * 3;
            if($chance >= $random) {
                $event->setCancelled();
                $entity->sendMessage(Translation::ORANGE . "You evaded the attack.");
                $damager = $event->getDamager();
                if($damager instanceof Player) {
                    $damager->sendMessage(Translation::ORANGE . "Your opponent has been drained.");
                }
            }
        };
    }
}