<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use core\item\ItemManager;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class EnchantmentBook extends CustomItem {

    const ENCHANTMENT = "Enchantment";

    /**
     * EnchantmentBook constructor.
     *
     * @param Enchantment $enchantment
     */
    public function __construct(Enchantment $enchantment) {
        $customName = TextFormat::RESET . TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "{$enchantment->getName()} Enchantment Book";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::DARK_PURPLE . "Enchantment: " . TextFormat::WHITE . $enchantment->getName();
        $lore[] = TextFormat::RESET . TextFormat::DARK_PURPLE . "Rarity: " . TextFormat::WHITE . ItemManager::rarityToString($enchantment->getRarity());
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Move book on top of an item to enchant it.";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Use " . TextFormat::YELLOW . TextFormat::BOLD . "/ceinfo" . TextFormat::RESET . TextFormat::WHITE . " to check what this enchantment does.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::ENCHANTMENT, $enchantment->getId());
        $tag->setString("UniqueId", uniqid());
        parent::__construct(self::ENCHANTED_BOOK, $customName, $lore);
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}