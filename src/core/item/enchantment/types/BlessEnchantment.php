<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\translation\Translation;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class BlessEnchantment extends Enchantment {

    /**
     * BlessEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::BLESS, "Bless", self::RARITY_MYTHIC, "Have a chance to gain regeneration and speed when health is low.", self::DAMAGE_BY, self::SLOT_ARMOR, 2);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) {
                return;
            }
            if($entity->getHealth() <= $event->getFinalDamage()) {
                $random = mt_rand(1, 10);
                $chance = $level;
                if($chance >= $random) {
                    $pk = new LevelSoundEventPacket();
                    $pk->position = $entity;
                    $pk->sound = LevelSoundEventPacket::SOUND_BEACON_POWER;
                    $entity->sendDataPacket($pk);
                    $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 100, 1));
                    $entity->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 100, 3));
                    $entity->sendMessage(Translation::ORANGE . "You've been blessed.");
                    $event->setCancelled();
                }
            }
        };
    }
}