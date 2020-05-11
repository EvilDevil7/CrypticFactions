<?php

declare(strict_types = 1);

namespace core\mask\masks;

use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class RabbitMask extends MaskAPI{

    /**
     * @return string
     */
    public function getName(): string{
        return "Rabbit Mask";
    }

    /**
     * @return int
     */
    public function getDamage(): int{
        return 6;
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
            "§r§fCarrots help your eyes!\nJump like a bunny!",
            "",
            TextFormat::BOLD . TextFormat::GREEN . "EFFECT",
            TextFormat::BOLD . TextFormat::GREEN . " * " . TextFormat::RESET . "Discover these for yourself ^_^",
        ];
    }

    /**
     * @param int $currentTick
     */
    public function tick(int $currentTick): void{
        foreach(Server::getInstance()->getOnlinePlayers() as $p){
            if($this->hasMask($p)){
                Utils::addEffect($p, Effect::JUMP, 6, 3);
                Utils::addEffect($p, Effect::SPEED, 6, 3);
            }
        }
    }
}