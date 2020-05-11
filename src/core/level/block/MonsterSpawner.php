<?php

declare(strict_types = 1);

namespace core\level\block;

use core\level\tile\MobSpawner;
use pocketmine\block\Block;
use pocketmine\block\MonsterSpawner as PMMonsterSpawner;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;
use ReflectionClass;
use ReflectionException;

class MonsterSpawner extends PMMonsterSpawner {

    /** @var int */
    private $entityId = -1;

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onActivate(Item $item, Player $player = null): bool {
        return false;
    }

    /**
     * @param Item $item
     * @param Block $blockReplace
     * @param Block $blockClicked
     * @param int $face
     * @param Vector3 $clickVector
     * @param Player|null $player
     *
     * @return bool
     */
    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool {
        $parent = parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        if($item->getNamedTag()->hasTag("EntityId")) {
            $this->entityId = $item->getNamedTag()->getInt("EntityId", -1);
            if($this->entityId > 10) {
                $this->getLevel()->setBlock($this, $this, true, false);
                $this->generateSpawnerTile();
            }
        }
        return $parent;
    }

    private function generateSpawnerTile(): void {
        $tile = $this->getLevel()->getTile($this);
        if(!$tile instanceof MobSpawner) {
            $nbt = MobSpawner::createNBT($this);
            $nbt->setString(Tile::TAG_ID, Tile::MOB_SPAWNER);
            /** @var MobSpawner $spawnerTile */
            $tile = Tile::createTile("MobSpawner", $this->getLevel(), $nbt);
        }
        $tile->setEntityId($this->entityId);
    }

    /**
     * @return int
     */
    public function getXpDropAmount(): int {
        return 0;
    }

    /**
     * @param Item $item
     *
     * @return array
     * @throws ReflectionException
     */
    public function getDrops(Item $item): array {
        $tile = $this->getLevel()->getTile($this);
        if($tile instanceof MobSpawner) {
            $item = Item::get(Item::MOB_SPAWNER, 0, $tile->getStack(), new CompoundTag("", [
                new IntTag("EntityId", $tile->entityId)
            ]));
            $item->setCustomName("§e" . $tile->getEntityType() . " Spawner§r");
            return [$item];
        }
        return [];
    }

    /**
     * @return string
     * @throws ReflectionException
     */
    public function getEntityType(): string {
        $class = new ReflectionClass(Entity::class);
        $ids = array_flip($class->getConstants());
        $id = $ids[$this->entityId];
        $name = implode("", explode(" ", ucwords(strtolower(implode(" ", explode("_", $id))))));
        return $name;
    }
}


