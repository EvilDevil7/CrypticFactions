<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class SacredStone extends CustomItem {

    const SACRED_STONE = "SacredStone";

    /**
     * SacredStone constructor.
     */
    public function __construct() {
        $customName = TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "Sacred Stone";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::YELLOW . "This stone contains a 1/7 chance to win a random Holy Box!";
        $lore[] = TextFormat::RESET . TextFormat::YELLOW . "Tap anywhere to uncover this stone.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setString(self::SACRED_STONE, self::SACRED_STONE);
        $tag->setString("UniqueId", uniqid());
        parent::__construct(self::NETHER_QUARTZ, $customName, $lore);
    }
}
