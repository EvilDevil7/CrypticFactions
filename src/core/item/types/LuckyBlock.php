<?php

declare(strict_types = 1);

namespace core\item\types;

use core\item\CustomItem;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class LuckyBlock extends CustomItem {

    const LUCK = "Luck";

    /**
     * LuckyBlock constructor.
     *
     * @param int $luck
     */
    public function __construct(int $luck) {
        $customName = "§l§eLucky Block§r";
        $unluck = 100 - $luck;
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "You have a " . TextFormat::BOLD . TextFormat::GREEN . "$luck%" . TextFormat::RESET . TextFormat::WHITE . "% of getting something good.§r";
        $lore[] = TextFormat::RESET . TextFormat::WHITE . "good and a " . TextFormat::BOLD . TextFormat::RED . "$unluck%" . TextFormat::RESET . TextFormat::WHITE . "% of getting something bad.§r";
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::YELLOW . "Break this for a surprise.§r";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::LUCK, $luck);
        parent::__construct(self::BLACK_GLAZED_TERRACOTTA, $customName, $lore);
    }
}
