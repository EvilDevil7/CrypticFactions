<?php

declare(strict_types = 1);

namespace core\price\event;

use core\CrypticPlayer;
use pocketmine\event\player\PlayerEvent;
use pocketmine\item\Item;

class ItemSellEvent extends PlayerEvent {

    /** @var Item */
    private $item;

    /** @var int */
    private $profit;

    /**
     * ItemSellEvent constructor.
     *
     * @param CrypticPlayer $player
     * @param Item          $item
     * @param int           $profit
     */
    public function __construct(CrypticPlayer $player, Item $item, int $profit) {
        $this->player = $player;
        $this->item = $item;
        $this->profit = $profit;
    }

    /**
     * @return Item
     */
    public function getItem(): Item {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getProfit(): int {
        return $this->profit;
    }
}