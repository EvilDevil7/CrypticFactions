<?php

declare(strict_types = 1);

namespace core\level;

use core\level\block\Generator;
use core\level\block\Hopper;
use core\level\block\Lava;
use core\level\block\LuckyBlock;
use core\level\block\MonsterSpawner;
use core\level\tile\MobSpawner;
use core\Cryptic;
use pocketmine\block\BlockFactory;
use pocketmine\block\GlowingRedstoneOre;
use pocketmine\block\RedstoneOre;
use pocketmine\item\Item;
use pocketmine\tile\Tile;
use ReflectionException;

class LevelManager {

    /** @var Cryptic */
    private $core;

    /**
     * LevelManager constructor.
     *
     * @param Cryptic $core
     *
     * @throws ReflectionException
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getServer()->getPluginManager()->registerEvents(new LevelListener($core), $core);
        $this->init();
    }

    /**
     * @throws ReflectionException
     */
    public function init(): void {
        BlockFactory::registerBlock(new Lava(), true);
        BlockFactory::registerBlock(new LuckyBlock(), true);
        BlockFactory::registerBlock(new Hopper(), true);
        BlockFactory::registerBlock(new MonsterSpawner(), true);
        BlockFactory::registerBlock(new class() extends RedstoneOre {

            /**
             * @param Item $item
             *
             * @return array
             */
            public function getDropsForCompatibleTool(Item $item): array {
                return [Item::get(Item::REDSTONE, 0, 1)];
            }
        }, true);
        BlockFactory::registerBlock(new class() extends GlowingRedstoneOre {

            /**
             * @param Item $item
             *
             * @return array
             */
            public function getDropsForCompatibleTool(Item $item): array {
                return [Item::get(Item::REDSTONE, 0, 1)];
            }
        }, true);
        BlockFactory::registerBlock(new Generator(Item::BLUE_GLAZED_TERRACOTTA, Item::get(Item::COAL, 0, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::BROWN_GLAZED_TERRACOTTA, Item::get(Item::COAL_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::GRAY_GLAZED_TERRACOTTA, Item::get(Item::DYE, 4, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::CYAN_GLAZED_TERRACOTTA, Item::get(Item::LAPIS_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::LIGHT_BLUE_GLAZED_TERRACOTTA, Item::get(Item::DYE, 4, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::GREEN_GLAZED_TERRACOTTA, Item::get(Item::LAPIS_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::LIME_GLAZED_TERRACOTTA, Item::get(Item::IRON_INGOT, 0, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::MAGENTA_GLAZED_TERRACOTTA, Item::get(Item::IRON_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::PINK_GLAZED_TERRACOTTA, Item::get(Item::GOLD_INGOT, 0, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::ORANGE_GLAZED_TERRACOTTA, Item::get(Item::GOLD_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::RED_GLAZED_TERRACOTTA, Item::get(Item::DIAMOND, 0, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::PURPLE_GLAZED_TERRACOTTA, Item::get(Item::DIAMOND_ORE, 0, 1), Generator::MINING), true);
        BlockFactory::registerBlock(new Generator(Item::SILVER_GLAZED_TERRACOTTA, Item::get(Item::EMERALD, 0, 1), Generator::AUTO), true);
        BlockFactory::registerBlock(new Generator(Item::WHITE_GLAZED_TERRACOTTA, Item::get(Item::EMERALD_ORE, 0, 1), Generator::MINING), true);
        Tile::registerTile(\core\level\tile\LuckyBlock::class);
        Tile::registerTile(\core\level\tile\Generator::class);
        Tile::registerTile(\core\level\tile\Hopper::class);
        Tile::registerTile(MobSpawner::class);
    }
}
