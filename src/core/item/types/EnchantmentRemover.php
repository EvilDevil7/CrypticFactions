<?php

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class EnchantmentRemover extends CustomItem {

    const SUCCESS_PERCENTAGE = "SuccessPercentage";

    /**
     * EnchantmentRemover constructor.
     *
     * @param int $success
     */
    public function __construct(int $success) {
        $customName = TextFormat::RESET . TextFormat::DARK_PURPLE . TextFormat::BOLD . "Enchantment Remover";
        $fail = 100 - $success;
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "You have a " . TextFormat::BOLD . TextFormat::GREEN . "$success%" . TextFormat::RESET . TextFormat::WHITE . "% of removing a";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "selected enchantment and a " . TextFormat::BOLD . TextFormat::RED . "$fail%" . TextFormat::RESET . TextFormat::WHITE . "% of";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "removing a random enchantment.";
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::YELLOW . "Tap the item at the Alchemist that you want to remove an enchantment from.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::SUCCESS_PERCENTAGE, $success);
        parent::__construct(self::SUGAR, $customName, $lore);
    }
}
