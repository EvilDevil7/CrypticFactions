<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class EndermanMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Enderman Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 8;
    }

    /**
     * @return array
     */
    public function getLore(): array{
        return [
            TextFormat::BOLD . TextFormat::GREEN . "\nRARITY",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Rare",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
            "§r§fHave a chance to look someone\nin the eye, and teleport!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Night Vision II",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::NIGHT_VISION, 15);
                Utils::addEffect($p, Effect::HEALTH_BOOST, 6);
                Utils::addEffect($p, Effect::SPEED, 6, 2);
            }
        }
    }
}