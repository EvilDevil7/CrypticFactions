<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\translation\Translation;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;

class ParalyzeEnchantment extends Enchantment {

    /**
     * ParalyzeEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::PARALYZE, "Paralyze", self::RARITY_RARE, "Give slowness for a long period.", self::DAMAGE, self::SLOT_BOW, 3);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$entity instanceof Player) or (!$damager instanceof Player)) {
                return;
            }
            if($event->getCause() !== EntityDamageByEntityEvent::CAUSE_PROJECTILE) {
                return;
            }
            if($entity->hasEffect(Effect::SLOWNESS)) {
                return;
            }
            $random = mt_rand(1, 30);
            $chance = $level * 3;
            if($chance >= $random) {
                $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), $level * 60, 1));
                $entity->sendMessage(Translation::ORANGE . "You are paralyzed.");
                $damager->sendMessage(Translation::ORANGE . "Your opponent is paralyzed.");
            }
        };
    }
}