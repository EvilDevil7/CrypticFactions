<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MinerMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Miner Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 3;
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
            "§r§fTo be able to mine\nlike a drill!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Haste II",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::HASTE, 6, 2);
                Utils::addEffect($p, Effect::SPEED, 6, 2);
                Utils::addEffect($p, Effect::NIGHT_VISION, 6, 2);
            }
        }
    }
}
