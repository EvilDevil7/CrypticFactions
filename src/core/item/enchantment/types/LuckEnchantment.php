<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\item\types\LuckyBlock;
use core\CrypticPlayer;
use core\translation\Translation;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;

class LuckEnchantment extends Enchantment {

    /**
     * LuckEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::LUCK, "Luck", self::RARITY_MYTHIC, "Increase your chance of getting a lucky block.", self::BREAK, self::SLOT_DIG, 1);
        $this->callable = function(BlockBreakEvent $event, int $level) {
            $block = $event->getBlock();
            $player = $event->getPlayer();
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            if($event->isCancelled()) {
                return;
            }
            if($block->getId() === Block::STONE) {
                if(mt_rand(1, 150) === mt_rand(1, 150)) {
                    $item = new LuckyBlock(mt_rand(0, 100));
                    $player->addReward($item->getItemForm());
                    $player->sendMessage(Translation::getMessage("luckyBlockFound"));
                    $player->addLuckyBlocksMined();
                }
            }
        };
    }
}