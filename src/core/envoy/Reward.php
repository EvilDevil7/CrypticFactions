<?php

declare(strict_types = 1);

namespace core\envoy;

use pocketmine\item\Item;

class Reward {

    /** @var string */
    private $name;

    /** @var Item */
    private $item;

    /** @var int */
    private $chance;

    /**
     * Reward constructor.
     *
     * @param string $name
     * @param Item $item
     * @param int $chance
     */
    public function __construct(string $name, Item $item, int $chance) {
        $this->name = $name;
        $this->item = $item;
        $this->chance = $chance;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return Item
     */
    public function getItem(): Item {
        return $this->item;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item): void {
        $this->item = $item;
    }

    /**
     * @return int
     */
    public function getChance(): int {
        return $this->chance;
    }
}