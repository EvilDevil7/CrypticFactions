<?php

declare(strict_types = 1);

namespace core\mask;

use core\mask\masks\ChefMask;
use core\mask\masks\ChickenMask;
use core\mask\masks\CreeperMask;
use core\mask\masks\DragonMask;
use core\mask\masks\EndermanMask;
use core\mask\masks\GodMask;
use core\mask\masks\MaskAPI;
use core\mask\masks\MinerMask;
use core\mask\masks\RabbitMask;
use core\mask\masks\SkeletonMask;
use core\mask\masks\WitchMask;
use core\mask\masks\WitherMask;
use core\mask\masks\ZombieMask;
use core\mask\task\MaskTask;
use core\Cryptic;
use pocketmine\Server;

class MaskManager{

    /** @var MaskAPI[] */
    private $masks = [];

    public function __construct(){
        Server::getInstance()->getPluginManager()->registerEvents(new MaskListener(), Cryptic::getInstance());
        Cryptic::getInstance()->getScheduler()->scheduleRepeatingTask(new MaskTask($this), 60);
        $this->register(new ChefMask());
        $this->register(new ChickenMask());
        $this->register(new CreeperMask());
        $this->register(new DragonMask());
        $this->register(new EndermanMask());
        $this->register(new GodMask());
        $this->register(new MinerMask());
        $this->register(new RabbitMask());
        $this->register(new SkeletonMask());
        $this->register(new WitchMask());
        $this->register(new WitherMask());
        $this->register(new ZombieMask());
    }

    /**
     * @param MaskAPI $mask
     */
    public function register(MaskAPI $mask): void{
        $this->masks[$mask->getDamage()] = $mask;
    }

    /**
     * @return MaskAPI[]
     */
    public function getMasks(): array{
        return $this->masks;
    }
}
