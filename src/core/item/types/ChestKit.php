<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use core\kit\Kit;
use pocketmine\nbt\tag\CompoundTag;

class ChestKit extends CustomItem {

    const KIT = "Kit";

    /**
     * ChestKit constructor.
     *
     * @param Kit $kit
     */
    public function __construct(Kit $kit) {
        $customName = "§l§6{$kit->getName()} Kit§r";
        $lore = [];
        $lore[] = "";
        $lore[] = "§r§7Tap anywhere to redeem§r";
        $lore[] = "§r§7A container that contains the {$kit->getName()} §r§7Kit.§r";
        $lore[] = "";
        $lore[] = "§eRarity: §7" . Kit::rarityToString($kit->getRarity());
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setString(self::KIT, $kit->getName());
        $tag->setString("UniqueId", uniqid());
        parent::__construct(self::CHEST_MINECART, $customName, $lore);
    }
}