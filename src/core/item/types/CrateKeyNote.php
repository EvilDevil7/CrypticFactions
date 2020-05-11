<?php

declare(strict_types = 1);

namespace core\item\types;

use core\crate\Crate;
use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class CrateKeyNote extends CustomItem {

    const CRATE = "Crate";

    const AMOUNT = "Amount";

    /**
     * CrateKeyNote constructor.
     *
     * @param Crate $crate
     * @param int $keys
     */
    public function __construct(Crate $crate, int $keys) {
        $customName = TextFormat::RESET . TextFormat::DARK_GREEN . TextFormat::BOLD . "{$crate->getName()} Key Note";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Tap anywhere to claim " . TextFormat::BOLD . TextFormat::GREEN . $keys . TextFormat::RESET . TextFormat::WHITE . " keys.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setString(self::CRATE, $crate->getName());
        $tag->setInt(self::AMOUNT, $keys);
        parent::__construct(self::PAPER, $customName, $lore);
    }
}