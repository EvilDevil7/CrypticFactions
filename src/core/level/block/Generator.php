<?php

declare(strict_types = 1);

namespace core\level\block;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\GlazedTerracotta;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class Generator extends GlazedTerracotta {

    const AUTO = 0;

    const MINING = 1;

    /** @var Item */
    private $generatedItem;

    /** @var int */
    private $type;

    /**
     * Generator constructor.
     *
     * @param int $id
     * @param Item $generatedItem
     * @param int $type
     */
    public function __construct(int $id, Item $generatedItem, int $type) {
        parent::__construct($id, 0, BlockFactory::get($id)->getName());
        $this->generatedItem = $generatedItem;
        $this->type = $type;
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onActivate(Item $item, Player $player = null): bool {
        $tile = $this->getLevel()->getTile($this);
        if(!$tile instanceof \core\level\tile\Generator) {
            /** @var CompoundTag $nbt */
            $nbt = new CompoundTag(
                "", [
                new StringTag(Tile::TAG_ID, "Generator"),
                new IntTag(Tile::TAG_X, (int)$this->x),
                new IntTag(Tile::TAG_Y, (int)$this->y),
                new IntTag(Tile::TAG_Z, (int)$this->z),
                new IntTag("stack", (int)1)
            ]);
            $tile = new \core\level\tile\Generator($this->getLevel(), $nbt);
            $this->getLevel()->addTile($tile);
        }
        return parent::onActivate($item, $player);
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
        $this->getLevel()->setBlock($this, $this, true, false);
        $tile = $this->getLevel()->getTile($this);
        if(!$tile instanceof \core\level\tile\Generator) {
            /** @var CompoundTag $nbt */
            $nbt = new CompoundTag("", [
                new StringTag(Tile::TAG_ID, "Generator"),
                new IntTag(Tile::TAG_X, (int)$this->x),
                new IntTag(Tile::TAG_Y, (int)$this->y),
                new IntTag(Tile::TAG_Z, (int)$this->z),
                new IntTag("stack", (int)1)
            ]);
            $tile = new \core\level\tile\Generator($this->getLevel(), $nbt);
            $this->getLevel()->addTile($tile);
        }
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onBreak(Item $item, Player $player = null): bool {
        $tile = $this->getLevel()->getTile($this);
        if($tile !== null) {
            $this->getLevel()->removeTile($tile);
        }
        return parent::onBreak($item, $player);
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
     * @return Item[]
     */
    public function getDrops(Item $item): array {
        $tile = $this->getLevel()->getTile($this);
        $count = 1;
        if($tile instanceof \core\level\tile\Generator) {
            $count = $tile->getStack();
        }
        $drop = Item::get($this->getItemId(), 0, $count);
        $drop->setCustomName(TextFormat::RESET . TextFormat::BOLD . TextFormat::DARK_RED . $this->generatedItem->getName() . " Generator");
        $lore = [];
        $lore[] = "";
        if($this->type === self::AUTO) {
            $lore[] = TextFormat::RESET . TextFormat::WHITE . "Place a " . TextFormat::RED . TextFormat::BOLD . "chest" . TextFormat::RESET . TextFormat::WHITE . " above generator to collect items.";
        }
        else {
            $lore[] = TextFormat::RESET . TextFormat::WHITE . "Creates " . TextFormat::RED . TextFormat::BOLD . $this->generatedItem->getName() . TextFormat::RESET . TextFormat::WHITE . " blocks above the generator.";
        }
        $drop->setLore($lore);
        return [$drop];
    }

    /**
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * @return Item
     */
    public function getGeneratedItem(): Item {
        return $this->generatedItem;
    }
}
