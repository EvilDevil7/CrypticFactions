<?php

declare(strict_types = 1);

namespace core\mask\masks;

use pocketmine\item\Item;
use pocketmine\Player;

class MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 0;
    }

    /**
     * @return array
     */
    public function getLore(): array{
        return [];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{

    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasMask(Player $player): bool{
        $helmet = $player->getArmorInventory()->getHelmet();
        if($helmet->getId() == Item::SKULL){
            if($helmet->getDamage() == $this->getDamage()) return true;
        }
        return false;
    }
}