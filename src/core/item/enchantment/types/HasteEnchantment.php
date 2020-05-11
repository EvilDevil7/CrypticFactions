<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\block\BlockBreakEvent;

class HasteEnchantment extends Enchantment {

    /**
     * HasteEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::HASTE, "Haste", self::RARITY_UNCOMMON, "Obtain haste.", self::BREAK, self::SLOT_DIG, 3);
        $this->callable = function(BlockBreakEvent $event, int $level) {
            $player = $event->getPlayer();
            if((!$player->hasEffect(Effect::HASTE)) or $player->getEffect(Effect::HASTE)->getDuration() <= 20) {
                $player->addEffect(new EffectInstance(Effect::getEffect(Effect::HASTE), 120, $level));
            }
            return;
        };
    }
}