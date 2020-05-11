<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class MoneyNote extends CustomItem {

    const BALANCE = "Balance";

    /**
     * MoneyNote constructor.
     *
     * @param int $amount
     */
    public function __construct(int $amount) {
        $customName = TextFormat::RESET . TextFormat::YELLOW . TextFormat::BOLD . "Money Note";
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "Tap anywhere to claim " . TextFormat::BOLD . TextFormat::GOLD . "$$amount" . TextFormat::RESET . TextFormat::WHITE . ".";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::BALANCE, $amount);
        parent::__construct(self::PAPER, $customName, $lore);
    }
}