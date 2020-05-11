<?php

declare(strict_types = 1);

namespace core\level\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Liquid;
use pocketmine\event\block\BlockSpreadEvent;

class Lava extends \pocketmine\block\Lava {

    protected function checkForHarden() {

    }

    /**
     * @param Block $block
     * @param int $newFlowDecay
     *
     */
    protected function flowIntoBlock(Block $block, int $newFlowDecay): void {
        if($this->canFlowInto($block) and !($block instanceof Liquid)) {
            $ev = new BlockSpreadEvent($block, $this, BlockFactory::get($this->getId(), $newFlowDecay));
            $ev->call();
            if(!$ev->isCancelled()){
                if($block->getId() > 0){
                    $this->level->useBreakOn($block);
                }
                $this->level->setBlock($block, $ev->getNewState(), true, true);
                $this->level->scheduleDelayedBlockUpdate($block, $this->tickRate());
            }
        }
    }
}
