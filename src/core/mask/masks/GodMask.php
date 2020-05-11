<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GodMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "God Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 10;
    }

    /**
     * @return array
     */
    public function getLore(): array{
        return [
            TextFormat::BOLD . TextFormat::GREEN . "\nRARITY",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Legendary",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
            "§r§fHave a chance to regain your strength when at a\nlow amount of health!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Regeneration II (0:10)",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II (0:10)",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::HEALTH_BOOST, 6);
                Utils::addEffect($p, Effect::SPEED, 6);
            }
        }
    }
}