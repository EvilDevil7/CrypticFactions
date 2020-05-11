<?php

declare(strict_types = 1);

namespace core\level\tile;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\tile\Spawnable;

class LuckyBlock extends Spawnable {

    const LUCK = "Luck";

    /** @var int */
    protected $luck = 0;

    /**
     * LuckyBlock constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    /**
     * @param int $luck
     */
    public function setLuck(int $luck) {
        $this->luck = $luck;
        $this->onChanged();
    }

    /**
     * @return int
     */
    public function getLuck(): int {
        return $this->luck;
    }

    /**
     * @param CompoundTag $nbt
     */
    public function readSaveData(CompoundTag $nbt): void {
        if($nbt->hasTag(self::LUCK, IntTag::class)) {
            $this->luck = $nbt->getInt(self::LUCK, 0);
        }
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::LUCK, $this->luck);
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function addAdditionalSpawnData(CompoundTag $nbt): void {
        $nbt->setInt(self::LUCK, $this->luck);
    }
}