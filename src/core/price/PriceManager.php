<?php

declare(strict_types = 1);

namespace core\price;

use core\item\types\ChestKit;
use core\Cryptic;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\Potion;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;

class PriceManager {

    /** @var Cryptic */
    private $core;

    /** @var ShopPlace[] */
    private $places = [];

    /** @var PriceEntry[] */
    private $sellables = [];

    /**
     * PriceManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->init();
    }

    public function init() {
        $this->places = [
            new ShopPlace("Market", [
                new PriceEntry(Item::get(Item::COAL, 0, 1), null, 25),
                new PriceEntry(Item::get(Item::REDSTONE, 0, 1), null, 50, 100),
				new PriceEntry(Item::get(Item::DYE, 4, 1), null, 150),
                new PriceEntry(Item::get(Item::IRON_INGOT, 0, 1), null, 100),
                new PriceEntry(Item::get(Item::GOLD_INGOT, 0, 1), null, 150),
                new PriceEntry(Item::get(Item::DIAMOND, 0, 1), null, 200),
                new PriceEntry(Item::get(Item::EMERALD, 0, 1), null, 225),
                new PriceEntry(Item::get(Item::OBSIDIAN, 0, 1), null, null, 175),
                new PriceEntry(Item::get(Item::TNT, 0, 1), null, null, 3125),
                new PriceEntry(Item::get(Item::WATER, 0, 1), null, null, 2500),
                new PriceEntry(Item::get(Item::HOPPER, 0, 1), null, null, 1000),
                new PriceEntry(Item::get(Item::CHEST, 0, 1), null, null, 100),
                new PriceEntry(Item::get(Item::HOPPER, 0, 1), null, null, 500),
                new PriceEntry(Item::get(Item::ENDER_PEARL, 0, 1), null, null, 100),
                new PriceEntry(Item::get(Item::STEAK, 0, 1), null, null, 1),
                new PriceEntry(Item::get(Item::TORCH, 0, 1), null, null, 5),
                new PriceEntry(Item::get(Item::GLOWSTONE, 0, 1), null, null, 12),
                new PriceEntry(Item::get(Item::SEA_LANTERN, 0, 1), null, null, 15),
                new PriceEntry(Item::get(Item::GRASS, 0, 1), null, 5, 10),
                new PriceEntry(Item::get(Item::COBBLESTONE, 0, 1), null, 3),
                new PriceEntry(Item::get(Item::WOOD, 0, 1), null, 6, 12),
                new PriceEntry(Item::get(Item::WOOD, 1, 1), null, 6, 12),
                new PriceEntry(Item::get(Item::WOOD, 2, 1), null, 6, 12),
                new PriceEntry(Item::get(Item::WOOD, 3, 1), null, 6, 12),
                new PriceEntry(Item::get(Item::STONE_BRICK, 0, 1), null, null, 12),
                new PriceEntry(Item::get(Item::STONE_BRICK, 1, 1), null, null, 12),
                new PriceEntry(Item::get(Item::STONE_BRICK, 2, 1), null, null, 12),
                new PriceEntry(Item::get(Item::END_BRICKS, 0, 1), null, null, 12),
                new PriceEntry(Item::get(Item::PRISMARINE, 0, 1), null, null, 24),
                new PriceEntry(Item::get(Item::PRISMARINE, 1, 1), null, null, 24),
                new PriceEntry(Item::get(Item::TERRACOTTA, 0, 1), null, null, 30),
                new PriceEntry(Item::get(Item::GLASS, 0, 1), null, null, 6),
                new PriceEntry(Item::get(Item::WOOL, 0, 1), null, null, 15),
                new PriceEntry(Item::get(Item::DYE, 16, 1), "Black Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 1, 1), "Red Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 2, 1), "Green Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 17, 1), "Brown Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 5, 1), "Purple Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 6, 1), "Cyan Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 7, 1), "Light Gray Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 8, 1), "Gray Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 9, 1), "Pink Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 10, 1), "Lime Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 11, 1), "Dandelion Yellow Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 12, 1), "Light Blue Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 13, 1), "Magenta Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 14, 1), "Orange Dye", null, 60),
                new PriceEntry(Item::get(Item::DYE, 19, 1), "White Dye", null, 60),
                new PriceEntry(Item::get(Item::NETHER_BRICK_BLOCK, 0, 1), null, null, 30),
                new PriceEntry(Item::get(Item::NETHERRACK, 0, 1), null, null, 12),
                new PriceEntry(Item::get(Item::QUARTZ_BLOCK, 0, 1), null, null, 60),
                new PriceEntry(Item::get(Item::QUARTZ_BLOCK, 1, 1), null, null, 60),
                new PriceEntry(Item::get(Item::SAND, 0, 1), null, null, 6),
                new PriceEntry(Item::get(Item::SAND, 1, 1), null, null, 8)
            ]),
            new ShopPlace("Black Market", [
                new PriceEntry(Item::get(Item::BOW, 0, 1), null, null, 5000),
                new PriceEntry(Item::get(Item::ARROW, 0, 1), null, null, 5),
                new PriceEntry(Item::get(Item::SPLASH_POTION, Potion::REGENERATION, 1), "Regeneration Splash Potion", null, 2500),
                new PriceEntry(Item::get(Item::SPLASH_POTION, Potion::NIGHT_VISION, 1), "Night Vision Splash Potion", null, 2500),
                new PriceEntry(Item::get(Item::SPLASH_POTION, Potion::HEALING, 1), "Instant Health Splash Potion", null, 3500),
                new PriceEntry(Item::get(Item::SPLASH_POTION, Potion::STRONG_SWIFTNESS, 1), "Speed Splash Potion", null, 3000),
                new PriceEntry(Item::get(Item::SPLASH_POTION, Potion::FIRE_RESISTANCE, 1), "Fire Resistance Splash Potion", null, 1000),
                new PriceEntry((new ChestKit($this->core->getKitManager()->getKitByName("Knight")))->getItemForm(), "Knight Kit", null, 50000),
                new PriceEntry((new ChestKit($this->core->getKitManager()->getKitByName("Wizard")))->getItemForm(), "Wizard Kit", null, 100000),
                new PriceEntry((new ChestKit($this->core->getKitManager()->getKitByName("King")))->getItemForm(), "King Kit", null, 150000),
                new PriceEntry((new ChestKit($this->core->getKitManager()->getKitByName("Mystic")))->getItemForm(), "Mystic Kit", null, 350000),
                new PriceEntry((new ChestKit($this->core->getKitManager()->getKitByName("Cryptic")))->getItemForm(), "Cryptic Kit", null, 500000)
            ]),
            new ShopPlace("Mining Generators", [
                new PriceEntry(Item::get(Item::BROWN_GLAZED_TERRACOTTA, 0, 1), "Coal Ore Block Generator", null, 100000),
                new PriceEntry(Item::get(Item::CYAN_GLAZED_TERRACOTTA, 0, 1), "Lapis Ore Block Generator", null, 200000, "permission.knight"),
                new PriceEntry(Item::get(Item::MAGENTA_GLAZED_TERRACOTTA, 0, 1), "Iron Ore Block Generator", null, 800000, "permission.wizard"),
                new PriceEntry(Item::get(Item::ORANGE_GLAZED_TERRACOTTA, 0, 1), "Gold Ore Block Generator", null, 1700000, "permission.king"),
                new PriceEntry(Item::get(Item::PURPLE_GLAZED_TERRACOTTA, 0, 1), "Diamond Ore Block Generator", null, 3000000, "permission.mystic"),
                new PriceEntry(Item::get(Item::WHITE_GLAZED_TERRACOTTA, 0, 1), "Emerald Ore Block Generator", null, 5000000, "permission.cryptic"),
            ]),
            new ShopPlace("Auto Generators", [
                new PriceEntry(Item::get(Item::BLUE_GLAZED_TERRACOTTA, 0, 1), "Coal Auto Generator", null, 160000),
                new PriceEntry(Item::get(Item::GRAY_GLAZED_TERRACOTTA, 0, 1), "Lapis Auto Generator", null, 300000, "permission.knight"),
                new PriceEntry(Item::get(Item::LIME_GLAZED_TERRACOTTA, 0, 1), "Iron Auto Generator", null, 600000, "permission.wizard"),
                new PriceEntry(Item::get(Item::PINK_GLAZED_TERRACOTTA, 0, 1), "Gold Auto Generator", null, 2000000, "permission.king"),
                new PriceEntry(Item::get(Item::RED_GLAZED_TERRACOTTA, 0, 1), "Diamond Auto Generator", null, 4000000, "permission.mystic"),
                new PriceEntry(Item::get(Item::SILVER_GLAZED_TERRACOTTA, 0, 1), "Emerald Auto Generator", null, 7000000, "permission.cryptic"),
            ]),
            new ShopPlace("Spawners", [
                new PriceEntry((Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::PIG)
                ])))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Pig Spawner"), "Pig Spawner", null, 100000, "permission.knight"),
                new PriceEntry(Item::get(Item::PORKCHOP, 0, 1), null, 35),
                new PriceEntry((Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::COW)
                ])))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Cow Spawner"), "Cow Spawner", null, 150000, "permission.wizard"),
                new PriceEntry(Item::get(Item::LEATHER, 0, 1), null, 40),
                new PriceEntry(Item::get(Item::RAW_BEEF, 0, 1), null, 35),
                new PriceEntry((Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::SPIDER)
                ])))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Spider Spawner"), "Spider Spawner", null, 700000, "permission.king"),
                new PriceEntry(Item::get(Item::STRING, 0, 1), null, 120),
                new PriceEntry((Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::BLAZE)
                ])))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Blaze Spawner"), "Blaze Spawner", null, 1700000, "permission.mystic"),
                new PriceEntry(Item::get(Item::BLAZE_ROD, 0, 1), null, 180),
                new PriceEntry((Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::IRON_GOLEM)
                ])))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Iron Golem Spawner"), "Iron Golem Spawner", null, 5000000, "permission.cryptic"),
                new PriceEntry(Item::get(Item::NETHER_STAR, 0, 1), null, 275),
                new PriceEntry(Item::get(Item::POPPY, 0, 1), null, 5),
            ])
        ];
        foreach($this->getAll() as $entry) {
            if($entry->getSellPrice() !== null) {
                $this->sellables[$entry->getItem()->getId()] = $entry;
            }
        }
    }

    /**
     * @return PriceEntry[]
     */
    public function getAll(): array {
        $all = [];
        foreach($this->places as $place) {
            $all = array_merge($all, $place->getEntries());
        }
        return $all;
    }

    /**
     * @return PriceEntry[]
     */
    public function getSellables(): array {
        return $this->sellables;
    }

    /**
     * @return ShopPlace[]
     */
    public function getPlaces(): array {
        return $this->places;
    }

    /**
     * @param string $name
     *
     * @return ShopPlace|null
     */
    public function getPlace(string $name): ?ShopPlace {
        foreach($this->places as $place) {
            if($place->getName() === $name) {
                return $place;
            }
        }
        return null;
    }
}