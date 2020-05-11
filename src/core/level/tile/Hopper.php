<?php

declare(strict_types = 1);

namespace core\level\tile;

use core\level\inventory\HopperInventory;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\Spawnable;
use pocketmine\tile\Tile;

class Hopper extends Spawnable implements Container, Nameable, InventoryHolder {

    use NameableTrait {
        addAdditionalSpawnData as addNameSpawnData;
    }
    use ContainerTrait;

    /** @var string */
    public const TAG_TRANSFER_COOLDOWN = "TransferCooldown";

    /** @var HopperInventory */
    protected $inventory;

    /** @var int */
    protected $transferCooldown = 8;

    /**
     * Hopper constructor.
     *
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->scheduleUpdate();
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function readSaveData(CompoundTag $nbt): void {
        $this->transferCooldown = $nbt->getInt(self::TAG_TRANSFER_COOLDOWN, 8);
        $this->inventory = new HopperInventory($this);
        $this->loadName($nbt);
        $this->loadItems($nbt);
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setInt(self::TAG_TRANSFER_COOLDOWN, $this->transferCooldown);
        $this->saveItems($nbt);
        $this->saveName($nbt);
    }

    public function close(): void {
        if(!$this->closed) {
            $this->inventory->removeAllViewers(true);
            $this->inventory = null;
            parent::close();
        }
    }

    /**
     * @return HopperInventory|Inventory
     */
    public function getInventory() {
        return $this->inventory;
    }

    /**
     * @return HopperInventory|Inventory
     */
    public function getRealInventory() {
        return $this->inventory;
    }

    /**
     * @return string
     */
    public function getDefaultName(): string {
        return "Hopper";
    }

    /**
     * @param CompoundTag $nbt
     */
    protected function addAdditionalSpawnData(CompoundTag $nbt): void {
        $this->addNameSpawnData($nbt);
    }

    /**
     * @return bool
     */
    public function onUpdate(): bool {
        $block = $this->getBlock();
        if(!$block instanceof \core\level\block\Hopper) {
            return false;
        }
        $area = clone $block->getBoundingBox();
        $area->maxY = ceil($area->maxY) + 1;
        $chunkEntities = array_filter(
            $this->getLevel()->getChunkEntities($this->x >> 4, $this->z >> 4), static function(Entity $entity): bool {
            return $entity instanceof ItemEntity and !$entity->isFlaggedForDespawn();
        }
        );
        /** @var ItemEntity $entity */
        foreach($chunkEntities as $entity) {
            if(!$entity->boundingBox->intersectsWith($area)) {
                continue;
            }
            $item = $entity->getItem();
            if(!($item instanceof Item) or $item->isNull()) {
                $entity->flagForDespawn();
                continue;
            }
            if($this->inventory->canAddItem($item)) {
                $this->inventory->addItem($item);
                $entity->flagForDespawn();
            }
        }
        if($this->transferCooldown !== 0) {
            $this->transferCooldown--;
            return true;
        }
        $aboveBlock = $this->getLevel()->getBlock($this->add(0, 1));
        $container = $this->getLevel()->getTile($aboveBlock);
        $pulledItem = false;
        if($container instanceof InventoryHolder && (!$container instanceof self || $aboveBlock->getDamage() !== Vector3::SIDE_DOWN)) {
            $inventory = $container->getInventory();
            $item = $this->getFirstItem($inventory);
            if($item !== null) {
                $pulledItem = $this->pullItem($item, $inventory);
            }
        }
        $pos = $this->asVector3()->getSide($block->getDamage());
        $tile = $this->level->getTileAt($pos->x, $pos->y, $pos->z);
        $belowTile = $this->getLevel()->getTile($this->subtract(0, 1));
        if((!$belowTile instanceof self || !$pulledItem) && $tile instanceof Tile and $tile instanceof InventoryHolder) {
            $item = $this->getFirstItem($this->inventory);
            if($item !== null) {
                $this->transferItem($item, $tile);
            }
        }
        return true;
    }

    /**
     * @param Item $trItem
     * @param Inventory $inventory
     *
     * @return bool
     */
    public function pullItem(Item $trItem, Inventory $inventory): bool {
        $inv = $this->getInventory();
        if($inv->canAddItem($trItem)) {
            $inv->addItem($trItem);
            $inventory->removeItem($trItem);
            return true;
        }
        return false;
    }

    /**
     * @param Item $trItem
     * @param InventoryHolder $inventoryHolder
     *
     * @return bool
     */
    public function transferItem(Item $trItem, InventoryHolder $inventoryHolder): bool {
        if($trItem->getCount() >= 4) {
            $count = 4;
        }
        else {
            $count = $trItem->getCount();
        }
        $item = clone $trItem;
        $item->setCount($count);
        $inv = $inventoryHolder->getInventory();
        if($inv->canAddItem($item)) {
            $inv->addItem($item);
            $this->inventory->removeItem($item);
            $this->resetTransferCooldown();
            if($inventoryHolder instanceof self) {
                $inventoryHolder->resetTransferCooldown();
            }
            return true;
        }
        return false;
    }

    public function resetTransferCooldown() {
        $this->transferCooldown = 8;
    }

    /**
     * @param Inventory $inventory
     *
     * @return Item|null
     */
    public function getFirstItem(Inventory $inventory): ?Item {
        foreach($inventory->getContents() as $slot) {
            if($slot !== null and !$slot->isNull()) {
                return $slot;
            }
        }
        return null;
    }
}