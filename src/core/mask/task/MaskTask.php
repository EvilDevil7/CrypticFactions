<?php

declare(strict_types = 1);

namespace core\mask\task;

use core\mask\MaskManager;
use pocketmine\scheduler\Task;

class MaskTask extends Task{

    /** @var MaskManager */
    private $manager;

    /**
     * MinerMask constructor.
     * @param MaskManager $manager
     */
    public function __construct(MaskManager $manager){
        $this->manager = $manager;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick): void{
        foreach($this->manager->getMasks() as $damage => $mask){
            $mask->tick($currentTick);
        }
    }
}
