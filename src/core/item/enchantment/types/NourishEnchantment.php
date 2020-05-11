<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class NourishEnchantment extends Enchantment {

    /**
     * NourishEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::NOURISH, "Nourish", self::RARITY_COMMON, "Have a chance restore hunger.", self::DAMAGE_BY, self::SLOT_ARMOR, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            if(!$entity instanceof Player) {
                return;
            }
            if($entity->getFood() === $entity->getMaxFood()) {
                return;
            }
            $random = mt_rand(1, 500);
            $chance = $level * 3;
            if($chance >= $random) {
                $pk = new LevelSoundEventPacket();
                $pk->position = $entity;
                $pk->sound = LevelSoundEventPacket::SOUND_BURP;
                $entity->sendDataPacket($pk);
                $entity->setFood($entity->getMaxFood());
            }
        };
    }
}