<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\CrypticPlayer;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\TextFormat;

class JackpotEnchantment extends Enchantment {

    /**
     * JackpotEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::JACKPOT, "Jackpot", self::RARITY_RARE, "Have a chance to earn money while mining.", self::BREAK, self::SLOT_DIG, 5);
        $this->callable = function(BlockBreakEvent $event, int $level) {
            $block = $event->getBlock();
            $player = $event->getPlayer();
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            if($event->isCancelled()) {
                return;
            }
            $amount = mt_rand(100, 10000);
            $amount *= $level;
            if($block->getId() === Block::STONE) {
                if(mt_rand(1, 300) === mt_rand(1, 300)) {
                    $player->addToBalance($amount);
                    $player->sendMessage(TextFormat::GREEN . " + $$amount");
                }
            }
        };
    }
}