<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use core\kit\Kit;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class HolyBox extends CustomItem {

    const SACRED_KIT = "SacredKit";

    /**
     * HolyBox constructor.
     *
     * @param Kit $kit
     */
    public function __construct(Kit $kit) {
        $customName = TextFormat::RESET . TextFormat::YELLOW . TextFormat::BOLD . "{$kit->getName()} Holy Box";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::GRAY . "Place in spawn to open this box for a chance to get a sacred kit permanently!";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setString(self::SACRED_KIT, $kit->getName());
        $tag->setString("UniqueId", uniqid());
        parent::__construct(self::CHEST, $customName, $lore);
    }
}