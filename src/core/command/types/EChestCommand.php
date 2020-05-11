<?php

namespace core\command\types;

use core\command\utils\Command;
use core\faction\Faction;
use core\CrypticPlayer;
use core\rank\Rank;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\EnderChest;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class EChestCommand extends Command {

    /**
     * EChestCommand constructor.
     */
    public function __construct() {
        parent::__construct("echest", "Open your ender chest.");
        $this->setAliases(["enderchest", "ec"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $nbt = new CompoundTag("", [new StringTag("id", Tile::CHEST), new StringTag("CustomName", "EnderChest"), new IntTag("x", (int)floor($sender->x)), new IntTag("y", (int)floor($sender->y) - 4), new IntTag("z", (int)floor($sender->z))]);
        /** @var EnderChest $tile */
        $tile = Tile::createTile("EnderChest", $sender->getLevel(), $nbt);
        $block = Block::get(Block::ENDER_CHEST);
        $block->x = (int)$tile->x;
        $block->y = (int)$tile->y;
        $block->z = (int)$tile->z;
        $block->level = $tile->getLevel();
        $block->level->sendBlocks([$sender], [$block]);
        $sender->getEnderChestInventory()->setHolderPosition($tile);
        $sender->addWindow($sender->getEnderChestInventory());
    }
}