<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Durable;
use pocketmine\Player;

class ShatterEnchantment extends Enchantment {

    /**
     * ShatterEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::SHATTER, "Shatter", self::RARITY_RARE, "Break your opponent's armor faster.", self::DAMAGE, self::SLOT_SWORD, 5);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) {
                return;
            }
            $damage = mt_rand(1, 20) * $level;
            foreach($entity->getArmorInventory()->getContents() as $armor) {
                if($armor instanceof Durable) {
                    $armor->applyDamage($damage);
                }
            }
            return;
        };
    }
}