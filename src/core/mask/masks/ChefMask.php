<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class ChefMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Chef Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 9;
    }

    /**
     * @return array
     */
    public function getLore(): array{
        return [
            TextFormat::BOLD . TextFormat::GREEN . "\nRARITY",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Common",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
            "§r§fYou will never\ngo hungry again!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Saturarion",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::SATURATION, 6);
            }
        }
    }
}