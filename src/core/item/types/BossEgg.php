<?php

namespace core\item\types;

use core\item\CustomItem;
use core\Cryptic;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class BossEgg extends CustomItem {

    const BOSS_ID = "BossId";

    /**
     * BossEgg constructor.
     *
     * @param int $bossId
     */
    public function __construct(int $bossId) {
        $customName = TextFormat::RESET . TextFormat::DARK_RED . TextFormat::BOLD . "Boss Egg";
        $boss = Cryptic::getInstance()->getCombatManager()->getBossNameByIdentifier($bossId);
        $boss = explode("\\", $boss);
        $boss = $boss[4];
        $lore = [];
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::RED . "Boss: " . TextFormat::WHITE . $boss;
        $lore[] = "";
        $lore[] = TextFormat::RESET . TextFormat::GRAY . "Click to spawn boss.";
        $this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
        /** @var CompoundTag $tag */
        $tag = $this->getNamedTagEntry(self::CUSTOM);
        $tag->setInt(self::BOSS_ID, $bossId);
        parent::__construct(self::SPAWN_EGG, $customName, $lore);
    }
}