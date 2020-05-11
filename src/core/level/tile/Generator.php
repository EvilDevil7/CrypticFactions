<?php

declare(strict_types=1);

namespace core\level\tile;

use pocketmine\block\Block;
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\tile\Container;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;

class Generator extends Tile {

    /** @var string */
    public const
        TAG_STACK = "Stack";


    /** @var int */
    public $generateTick = 0;

    /** @var int */
    private $stack = 1;

    /**
     * Generator constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->scheduleUpdate();
    }

    /**
     * @return bool
     */
    public function onUpdate(): bool {
        if($this->generateTick++ >= (int)round(120 / $this->stack)) {
            if($this->isClosed()) {
                return false;
            }
            $block = $this->getBlock();
            if(!$block instanceof \core\level\block\Generator) {
                return false;
            }
            if($block->getType() === \core\level\block\Generator::AUTO) {
                $vector = $block->getSide(Vector3::SIDE_UP);
                $tile = $this->getLevel()->getTile($vector);
                if(!$tile instanceof Container) {
                    return true;
                }
                $inventory = $tile->getInventory();
                if($inventory->canAddItem($block->getGeneratedItem())) {
                    $inventory->addItem($block->getGeneratedItem());
                    $this->generateTick = 0;
                }
                return true;
            }
            $vector = $block->getSide(Vector3::SIDE_UP);
            if($this->getLevel()->getBlock($vector)->getId() === Block::AIR) {
                $this->getLevel()->setBlock($vector, $block->getGeneratedItem()->getBlock());
                $this->generateTick = 0;
            }
            return true;
        }
        return true;
    }

    /**
     * @return int
     */
    public function getStack(): int {
        return $this->stack;
}

    /**
     * @param int $stack
     */
    public function setStack(int $stack): void {
        $this->stack = $stack;
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void {
        if($nbt->hasTag(self::TAG_STACK)) {
            $this->stack = $nbt->getInt(self::TAG_STACK);
        }
        $this->scheduleUpdate();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::TAG_STACK, $this->stack);
        $this->scheduleUpdate();
    }
}