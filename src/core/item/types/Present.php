<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class Present extends CustomItem {

    const PRESENT = "Present";

    /**
     * Present constructor.
     */
    public function __construct() {
        $customName = TextFormat::RESET . TextFormat::DARK_RED . TextFormat::BOLD . "Present";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "Possible rewards:";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "God Rank";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Warlord Rank";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Holy Box";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Enchantment Book";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Sacred Stone";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Lucky Block";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Sell Wand";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Money";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "Crate Keys";
        $lore[] = TextFormat::RESET . TextFormat::WHITE .  TextFormat::BOLD . " * " . TextFormat::RESET . TextFormat::GRAY . "More rewards to come!";
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Tap anywhere to claim your present.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setString(self::PRESENT, "Present");
        parent::__construct(self::CHEST, $customName, $lore);
    }
}
