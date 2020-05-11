<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SkeletonMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Skeleton Mask";
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
        return [
            TextFormat::BOLD . TextFormat::GREEN . "\nRARITY",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Rare",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "ABILITY",
            "§r§fLook them in the eye\nand shoot an arrow at them!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed III",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Jump Boost",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Night Vision",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::SPEED, 6, 3);
                Utils::addEffect($p, Effect::HASTE, 6, 2);
                Utils::addEffect($p, Effect::JUMP, 6);
                Utils::addEffect($p, Effect::NIGHT_VISION, 15);
            }
        }
    }
}