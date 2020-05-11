<?php

declare(strict_types = 1);

namespace core\level\block;

use pocketmine\block\Block;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;

class Hopper extends Transparent {

    /** @var int */
    protected $id = self::HOPPER_BLOCK;

    /**
     * Hopper constructor.
     *
     * @param int $meta
     */
    public function __construct(int $meta = 0) {
        $this->meta = $meta;
    }

    /**
     * @return bool
     */
    public function canBeActivated(): bool {
        return true;
    }

    /**
     * @return int
     */
    public function getToolType(): int {
        return BlockToolType::TYPE_PICKAXE;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Hopper";
    }

    /**
     * @return float
     */
    public function getHardness(): float {
        return 3;
    }

    /**
     * @return float
     */
    public function getBlastResistance(): float {
        return 24;
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onActivate(Item $item, Player $player = null): bool {
        if($player instanceof Player) {
            $t = $this->getLevel()->getTile($this);
            if($t instanceof \core\level\tile\Hopper) {
                if($player->isCreative()) {
                    return true;
                }
                $player->addWindow($t->getInventory());
            }
            else {
                $nbt = new CompoundTag(
                    "", [
                        new ListTag("Items", []),
                        new StringTag("id", "Hopper"),
                        new IntTag("x", $this->x),
                        new IntTag("y", $this->y),
                        new IntTag("z", $this->z),
                    ]
                );
                /** @var \core\level\tile\Hopper $t */
                $t = Tile::createTile("Hopper", $this->getLevel(), $nbt);
                if($player->isCreative()) {
                    return true;
                }
                $player->addWindow($t->getInventory());
            }
        }
        return true;
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
        $faces = [
            0 => 0,
            1 => 0,
            2 => 3,
            3 => 2,
            4 => 5,
            5 => 4,
        ];
        $this->meta = $faces[$face];
        $this->getLevel()->setBlock($blockReplace, $this, true, true);
        $nbt = new CompoundTag(
            "", [
                new ListTag("Items", []),
                new StringTag("id", "Hopper"),
                new IntTag("x", $this->x),
                new IntTag("y", $this->y),
                new IntTag("z", $this->z),
            ]
        );
        if($item->hasCustomName()) {
            $nbt->setString("CustomName", $item->getCustomName());
        }
        if($item->hasCustomBlockData()) {
            foreach($item->getCustomBlockData() as $key => $v) {
                $nbt->{$key} = $v;
            }
        }
        Tile::createTile("Hopper", $this->getLevel(), $nbt);
        return true;
    }

    /**
     * @param Item $item
     *
     * @return array
     */
    public function getDrops(Item $item): array {
        return [Item::get(Item::HOPPER, 0, 1)];
    }
}