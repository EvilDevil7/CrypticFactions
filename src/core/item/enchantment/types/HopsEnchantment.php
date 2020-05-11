<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerMoveEvent;

class HopsEnchantment extends Enchantment {

    /**
     * HopsEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::HOPS, "Hops", self::RARITY_UNCOMMON, "Obtain jump boost.", self::MOVE, self::SLOT_FEET, 2);
        $this->callable = function(PlayerMoveEvent $event, int $level) {
            $player = $event->getPlayer();
            if((!$player->hasEffect(Effect::JUMP_BOOST)) or $player->getEffect(Effect::JUMP_BOOST)->getDuration() <= 20) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP_BOOST), 120, $level));
            }
            return;
        };
    }
}