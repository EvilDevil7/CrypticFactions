<?php

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class SellWand extends CustomItem {

    const USES = "Uses";

    /**
     * SellWand constructor.
     *
     * @param int $uses
     */
    public function __construct(int $uses) {
        $customName = TextFormat::RESET . TextFormat::DARK_AQUA . TextFormat::BOLD . "Sell Wand";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::AQUA . "Uses: " . TextFormat::WHITE . $uses;
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Tap a chest to sell all It's sellable contents.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::USES, $uses);
        parent::__construct(self::DIAMOND_HOE, $customName, $lore);
    }
}