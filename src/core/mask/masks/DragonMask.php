<?php

declare(strict_types = 1);

namespace core\mask\masks;

use core\CrypticPlayer;
use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class DragonMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Dragon Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 5;
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
            "§r§fGain many effects and gain ability to fly!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Speed II",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Regeneration I",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Gain 5 Extra Health",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Be able to fly",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                if($p instanceof CrypticPlayer){
                    if($p->getAllowFlight() == false and !$p->isTagged()){
                        $p->setAllowFlight(true);
                        $p->setFlying(true);
                    }
                }
                Utils::addEffect($p, Effect::HEALTH_BOOST, 6, 3);
                Utils::addEffect($p, Effect::REGENERATION, 6);
                Utils::addEffect($p, Effect::SPEED, 6, 4);
            }
        }
    }
}
