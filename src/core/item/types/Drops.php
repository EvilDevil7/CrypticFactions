<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

class Drops extends CustomItem {

    const ITEM_LIST = "ItemList";

    /**
     * Drops constructor.
     *
     * @param string $player
     * @param Item[] $items
     */
    public function __construct(string $player, array $items) {
        $customName = "§l§6$player's Drops§r";
        $lore = [];
        $lore[] = "";
        $lore[] = "§r§7Tap anywhere to redeem§r";
        $lore[] = "§r§7A container that contains " . $player . "'s inventory drops.§r";
        $lore[] = "";
        $lore[] = "§r§cBe sure to clear your inventory!§r";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tags = [];
        foreach($items as $item) {
            $tags[] = $item->nbtSerialize();
        }
        $tag->setTag(new ListTag(self::ITEM_LIST, $tags));
        parent::__construct(self::NETHER_STAR, $customName, $lore);
    }
}