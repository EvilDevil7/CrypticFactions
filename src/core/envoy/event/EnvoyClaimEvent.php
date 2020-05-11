<?php

declare(strict_types = 1);

namespace core\envoy\event;

use core\envoy\Reward;
use core\CrypticPlayer;
use pocketmine\event\player\PlayerEvent;

class EnvoyClaimEvent extends PlayerEvent {

    /** @var Reward[] */
    private $items = [];

    /**
     * ItemBuyEvent constructor.
     *
     * @param CrypticPlayer $player
     * @param Reward[]      $items
     */
    public function __construct(CrypticPlayer $player, array $items) {
        $this->player = $player;
        $this->items = $items;
    }

    /**
     * @return Reward[]
     */
    public function getItems(): array {
        return $this->items;
    }

    /**
     * @param Reward[] $items
     */
    public function setItems(array $items): void {
        $this->items = $items;
    }
}
