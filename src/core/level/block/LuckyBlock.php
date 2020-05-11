<?php

declare(strict_types = 1);

namespace core\level\block;

use core\crate\Crate;
use core\entity\types\PrimedTNT;
use core\item\CustomItem;
use core\item\enchantment\EnchantmentIdentifiers;
use core\item\ItemManager;
use core\item\types\ChestKit;
use core\item\types\CrateKeyNote;
use core\item\types\EnchantmentBook;
use core\item\types\MoneyNote;
use core\item\types\SellWand;
use core\item\types\XPNote;
use core\CrypticPlayer;
use pocketmine\block\Block;
use pocketmine\block\GlazedTerracotta;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class LuckyBlock extends GlazedTerracotta {

    /** @var callable[] */
    private $goodRewards = [];

    /** @var callable[] */
    private $badRewards = [];

    /**
     * LuckyBlock constructor.
     */
    public function __construct() {
        parent::__construct(Block::BLACK_GLAZED_TERRACOTTA, 0, "Black Glazed Terracotta");
        $this->goodRewards = [
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new MoneyNote(mt_rand(1000, 50000)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new XPNote(mt_rand(1000, 10000)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new CrateKeyNote($player->getCore()->getCrateManager()->getCrate(Crate::RARE), mt_rand(2, 5)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new CrateKeyNote($player->getCore()->getCrateManager()->getCrate(Crate::LEGENDARY), mt_rand(2, 5)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new CrateKeyNote($player->getCore()->getCrateManager()->getCrate(Crate::COMMON), mt_rand(2, 5)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new SellWand(5))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new CrateKeyNote($player->getCore()->getCrateManager()->getCrate(Crate::EPIC), mt_rand(2, 5)))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new ChestKit($player->getCore()->getKitManager()->getKitByName("Wizard")))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new ChestKit($player->getCore()->getKitManager()->getKitByName("King")))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, Item::get(Item::TNT, 0, 16));
            },
            function(CrypticPlayer $player, Position $position): void {
                $position->level->dropItem($position, Item::get(Item::GOLDEN_APPLE, 0, 32));
            },
        ];
        $this->badRewards = [
            function(CrypticPlayer $player, Position $position): void {
                $player->teleport($player->getServer()->getDefaultLevel()->getSpawnLocation());
            },
            function(CrypticPlayer $player, Position $position): void {
                $player->setHealth(6);
            },
            function(CrypticPlayer $player, Position $position): void {
                $player->setFood(0);
            },
            function(CrypticPlayer $player, Position $position): void {
                $nbt = Entity::createBaseNBT($player->asPosition());
                $nbt->setShort("Fuse", 40);
                $level = $player->getLevel();
                for($i = 0; $i <= 3; ++$i) {
                    $entity = new PrimedTNT($level, $nbt);
                    $level->addEntity($entity);
                    $entity->spawnToAll();
                }
            },
            function(CrypticPlayer $player, Position $position): void {
                $effects = [
                    new EffectInstance(Effect::getEffect(Effect::POISON), 600, 1),
                    new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 200, 1)
                ];
                $player->addEffect($effects[array_rand($effects)]);
            },
            function(CrypticPlayer $player, Position $position): void {
                $effects = [
                    new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 600, 1),
                    new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 600, 1)
                ];
                foreach($effects as $effect) {
                    $player->addEffect($effect);
                }
            }
        ];
    }

    /**
     * @param Item $item
     * @param Player|null $player
     *
     * @return bool
     */
    public function onBreak(Item $item, Player $player = null): bool {
        if(!$player instanceof CrypticPlayer) {
            return false;
        }
        $tile = $this->getLevel()->getTile($this);
        $add = $item->getEnchantmentLevel(EnchantmentIdentifiers::CHARM) * 5;
        if($tile instanceof \core\level\tile\LuckyBlock) {
            $luck = $tile->getLuck() + $add;
            $this->getLevel()->removeTile($tile);
        }
        else {
            $luck = mt_rand(0, 100) + $add;
        }
        if(mt_rand(0, 100) <= $luck) {
            $reward = $this->goodRewards[array_rand($this->goodRewards)];
            $pk = new LevelSoundEventPacket();
            $pk->position = $player;
            $pk->sound = LevelSoundEventPacket::SOUND_BLAST;
            $player->sendDataPacket($pk);
        }
        else {
            $reward = $this->badRewards[array_rand($this->badRewards)];
            $pk = new LevelSoundEventPacket();
            $pk->position = $player;
            $pk->sound = LevelSoundEventPacket::SOUND_RAID_HORN;
            $player->sendDataPacket($pk);
        }
        $reward($player, $this->asPosition());
        return parent::onBreak($item, $player);
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
        $tag = $item->getNamedTagEntry(CustomItem::CUSTOM);
        if($tag instanceof CompoundTag) {
            $luck = $tag->getInt(\core\item\types\LuckyBlock::LUCK);
        }
        else {
            $luck = mt_rand(0, 100);
        }
        $tile = $this->getLevel()->getTile($this);
        if(!$tile instanceof \core\level\tile\LuckyBlock) {
            /** @var CompoundTag $nbt */
            $nbt = new CompoundTag("", [
                new StringTag(Tile::TAG_ID, "LuckyBlock"),
                new IntTag(Tile::TAG_X, (int)$this->x),
                new IntTag(Tile::TAG_Y, (int)$this->y),
                new IntTag(Tile::TAG_Z, (int)$this->z),
                new IntTag(\core\level\tile\LuckyBlock::LUCK, (int)$luck),
            ]);
            $tile = new \core\level\tile\LuckyBlock($this->getLevel(), $nbt);
            $this->getLevel()->addTile($tile);
        }
        return parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }


    /**
     * @param Item $item
     *
     * @return Item[]
     */
    public function getDrops(Item $item): array {
        return [];
    }
}