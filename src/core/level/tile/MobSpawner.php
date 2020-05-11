<?php

declare(strict_types=1);

namespace core\level\tile;

use core\entity\EntityManager;
use core\CrypticPlayer;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityIds;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ShortTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Spawnable;
use ReflectionClass;
use ReflectionException;

class MobSpawner extends Spawnable {

    /** @var string */
    public const
        TAG_ENTITY_ID = "EntityId",
        TAG_SPAWN_COUNT = "SpawnCount",
        TAG_SPAWN_RANGE = "SpawnRange",
        TAG_MIN_SPAWN_DELAY = "MinSpawnDelay",
        TAG_MAX_SPAWN_DELAY = "MaxSpawnDelay",
        TAG_STACK = "Stack",
        TAG_DELAY = "Delay";

    /** @var int */
    public $entityId = 0;

    /** @var int */
    protected $spawnCount = 8;

    /** @var int */
    protected $spawnRange = 4;

    /** @var int */
    protected $minSpawnDelay = 200;

    /** @var int */
    protected $maxSpawnDelay = 400;

    /** @var int */
    protected $delay;

    /** @var int */
    protected $stack = 1;

    /**
     * @return string
     */
    public function getName(): string {
        return "Monster Spawner";
    }

    /**
     * @return bool
     */
    public function onUpdate(): bool {
        if($this->isClosed()) {
            return false;
        }
        $this->timings->startTiming();
        if($this->delay <= 0) {
            $success = false;
            $block = $this->getBlock();
            $bb = $this->getBlock()->getBoundingBox();
            if(is_null($block) || is_null($bb)) {
                return true;
            }
            $bb = $bb->expandedCopy(16, 16, 16);
            foreach($this->getLevel()->getNearbyEntities($bb) as $e) {
                if($e->namedtag->hasTag(EntityManager::STACK_TAG) and $e instanceof Living and $e::NETWORK_ID === $this->entityId) {
                    EntityManager::increaseStackSize($e, $this->spawnCount * $this->stack);
                    $success = true;
                    break;
                }
            }
            if(!$success) {
                $pos = $this->add(0, 1, 0);
                $target = $this->getLevel()->getBlock($pos);
                if($target->getId() == Item::AIR) {
                    $success = true;
                    $entity = Entity::createEntity($this->getEntityId(), $this->getLevel(), Entity::createBaseNBT($target->add(0.5, 0, 0.5), null, lcg_value() * 360, 0));
                    if($entity instanceof Living) {
                        EntityManager::increaseStackSize($entity, $this->spawnCount * $this->stack);
                        $entity->spawnToAll();
                    }
                }
            }
            if($success) {
                $this->generateRandomDelay();
            }
        }
        else {
            $this->delay--;
        }
        $this->timings->stopTiming();
        return true;
    }

    /**
     * @return bool
     */
    public function canUpdate(): bool {
        if($this->getEntityId() !== 0 && $this->getLevel()->isChunkLoaded($this->getX() >> 4, $this->getZ() >> 4)) {
            $hasPlayer = false;
            $count = 0;
            foreach($this->getLevel()->getEntities() as $e) {
                if($e instanceof CrypticPlayer && $e->distance($this) <= 15) {
                    $hasPlayer = true;
                }
                if($e::NETWORK_ID == $this->getEntityId()) {
                    $count++;
                }
            }
            return ($hasPlayer && $count < 15);
        }
        return false;
    }

    /**
     * @return int
     */
    protected function generateRandomDelay(): int {
        return ($this->delay = mt_rand($this->getMinSpawnDelay(), $this->getMaxSpawnDelay()));
    }

    /**
     * @param CompoundTag $nbt
     */
    public function addAdditionalSpawnData(CompoundTag $nbt): void {
        $this->applyBaseNBT($nbt);
    }

    /**
     * @param CompoundTag $nbt
     */
    private function applyBaseNBT(CompoundTag &$nbt): void {
        $nbt->setInt(self::TAG_ENTITY_ID, $this->getEntityId());
        $nbt->setInt(self::TAG_SPAWN_COUNT, $this->getSpawnCount());
        $nbt->setInt(self::TAG_SPAWN_RANGE, $this->getSpawnRange());
        $nbt->setInt(self::TAG_MIN_SPAWN_DELAY, $this->getMinSpawnDelay());
        $nbt->setInt(self::TAG_MAX_SPAWN_DELAY, $this->getMaxSpawnDelay());
        $nbt->setInt(self::TAG_DELAY, $this->getDelay());
        $nbt->setInt(self::TAG_STACK, $this->getStack());
    }

    /**
     * @return int
     */
    public function getEntityId(): int {
        return $this->entityId;
    }

    /**
     * @param int $entityId
     */
    public function setEntityId(int $entityId): void {
        $this->entityId = $entityId;
        $this->onChanged(); // this needs to be sent to the client so the entity animation updates too
        $this->scheduleUpdate();
    }

    /**
     * @return int
     */
    public function getSpawnCount(): int {
        return $this->spawnCount;
    }

    /**
     * @param int $spawnCount
     */
    public function setSpawnCount(int $spawnCount): void {
        $this->spawnCount = $spawnCount;
    }

    /**
     * @return int
     */
    public function getSpawnRange(): int {
        return $this->spawnRange;
    }

    /**
     * @param int $spawnRange
     */
    public function setSpawnRange(int $spawnRange): void {
        $this->spawnRange = $spawnRange;
    }

    /**
     * @return int
     */
    public function getMinSpawnDelay(): int {
        return $this->minSpawnDelay;
    }

    /**
     * @param int $minSpawnDelay
     */
    public function setMinSpawnDelay(int $minSpawnDelay): void {
        $this->minSpawnDelay = $minSpawnDelay;
    }

    /**
     * @return int
     */
    public function getMaxSpawnDelay(): int {
        return $this->maxSpawnDelay;
    }

    /**
     * @param int $maxSpawnDelay
     */
    public function setMaxSpawnDelay(int $maxSpawnDelay): void {
        $this->maxSpawnDelay = $maxSpawnDelay;
    }

    /**
     * @return int
     */
    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @param int $delay
     */
    public function setDelay(int $delay): void {
        $this->delay = $delay;
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
    protected function readSaveData(CompoundTag $nbt): void {
        if($this->delay === null) {
            $this->generateRandomDelay();
        }
        if($nbt->hasTag(self::TAG_ENTITY_ID)) {
            $this->entityId = $nbt->getInt(self::TAG_ENTITY_ID);
        }
        if($nbt->hasTag(self::TAG_SPAWN_COUNT)) {
            $this->spawnCount = $nbt->getInt(self::TAG_SPAWN_COUNT);
        }
        if($nbt->hasTag(self::TAG_SPAWN_RANGE)) {
            $this->spawnRange = $nbt->getInt(self::TAG_SPAWN_RANGE);
        }
        if($nbt->hasTag(self::TAG_MIN_SPAWN_DELAY)) {
            $this->minSpawnDelay = $nbt->getInt(self::TAG_MIN_SPAWN_DELAY);
        }
        if($nbt->hasTag(self::TAG_MAX_SPAWN_DELAY)) {
            $this->maxSpawnDelay = $nbt->getInt(self::TAG_MAX_SPAWN_DELAY);
        }
        if($nbt->hasTag(self::TAG_DELAY)) {
            $this->delay = $nbt->getInt(self::TAG_DELAY);
        }
        if($nbt->hasTag(self::TAG_STACK)) {
            $this->stack = $nbt->getInt(self::TAG_STACK);
        }
        $this->scheduleUpdate();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $this->applyBaseNBT($nbt);
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getEntityType(): string {
        $class = new ReflectionClass(EntityIds::class);
        $ids = array_flip($class->getConstants());
        if(!isset($ids[$this->entityId])) {
            return "";
        }
        $id = $ids[$this->entityId];
        $name = implode("", explode(" ", ucwords(strtolower(implode(" ", explode("_", $id))))));
        return $name;
    }
}
