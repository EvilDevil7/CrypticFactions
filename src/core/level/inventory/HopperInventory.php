<?php

declare(strict_types = 1);

namespace core\level\inventory;

use core\level\tile\Hopper;
use pocketmine\inventory\ContainerInventory;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class HopperInventory extends ContainerInventory {

    /**
     * HopperInventory constructor.
     *
     * @param Hopper $tile
     */
    public function __construct(Hopper $tile) {
        parent::__construct($tile);
    }

    /**
     * @return Vector3
     */
    public function getHolder() {
        return $this->holder;
    }

    /**
     * @return int
     */
    public function getDefaultSize(): int{
        return 5;
    }

    /**
     * @return int
     */
    public function getNetworkType(): int{
        return WindowTypes::HOPPER;
    }

    /**
     * @return string
     */
    public function getName(): string{
        return "Hopper";
    }
}
