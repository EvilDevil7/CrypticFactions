<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\player\PlayerMoveEvent;

class PerceptionEnchantment extends Enchantment {

    /**
     * PerceptionEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::PERCEPTION, "Perception", self::RARITY_COMMON, "Obtain night vision.", self::MOVE, self::SLOT_HEAD, 1);
        $this->callable = function(PlayerMoveEvent $event, int $level) {
            $player = $event->getPlayer();
            if(!$player->hasEffect(Effect::NIGHT_VISION)) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 100000000, 0));
            }
            return;
        };
    }
}