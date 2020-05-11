<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\translation\Translation;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class WitherEnchantment extends Enchantment {

    /**
     * WitherEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::WITHER, "Wither", self::RARITY_RARE, "Have a chance to give off a wither effect to your opponent.", self::DAMAGE, self::SLOT_SWORD, 5);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$entity instanceof Player) or (!$damager instanceof Player)) {
                return;
            }
            if($entity->hasEffect(Effect::WITHER)) {
                return;
            }
            $random = mt_rand(1, 200);
            $chance = $level * 3;
            if($chance >= $random) {
                $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::WITHER), $level * 40, 1));
                $entity->sendMessage(Translation::ORANGE . "You are withering.");
                $damager->sendMessage(Translation::ORANGE . "Your opponent is withering.");
            }
        };
    }
}
