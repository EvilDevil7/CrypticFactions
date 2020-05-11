<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\translation\Translation;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class StunEnchantment extends Enchantment {

    /**
     * StunEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::STUN, "Stun", self::RARITY_MYTHIC, "Have a chance to stun your opponent.", self::DAMAGE, self::SLOT_SWORD, 5);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$entity instanceof Player) or (!$damager instanceof Player)) {
                return;
            }
            if($entity->hasEffect(Effect::BLINDNESS) and $entity->hasEffect(Effect::SLOWNESS)) {
                return;
            }
            $random = mt_rand(1, 200);
            $chance = $level * 3;
            if($chance >= $random) {
                $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), $level * 20, 1));
                $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), $level * 20, $level));
                $entity->sendMessage(Translation::ORANGE . "You are stunned.");
                $damager->sendMessage(Translation::ORANGE . "Your opponent is stunned.");
            }
        };
    }
}