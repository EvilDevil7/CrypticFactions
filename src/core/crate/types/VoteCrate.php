<?php

declare(strict_types = 1);

namespace core\crate\types;

use core\crate\Crate;
use core\crate\Reward;
use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\item\types\XPNote;
use core\CrypticPlayer;
use core\item\types\ChestKit;
use core\item\types\SellWand;
use core\Cryptic;
use libs\utils\UtilsException;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;

class VoteCrate extends Crate {

    /**
     * VoteCrate constructor.
     * @param Position $position
     */
    public function __construct(Position $position) {
        parent::__construct(self::VOTE, $position, [
            new Reward("$1,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(1000);
            }, 50),
            new Reward("$2,500", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(2000);
            }, 50),
            new Reward("$5,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(2000);
            }, 50),
            new Reward("$10,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(2000);
            }, 50),
            new Reward("1,000 XP Note", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new XPNote(1000))->getItemForm());
            }, 100),
            new Reward("Pig Spawner", Item::get(Item::MOB_SPAWNER, 0, 1), function(CrypticPlayer $player): void {
                $item = Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::PIG)
                ]));
                $item->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Pig Spawner");
                $player->getInventory()->addItem($item);
            }, 25),
            new Reward("x32 Obsidian", Item::get(Item::OBSIDIAN, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::OBSIDIAN, 0, 32));
            }, 85),
            new Reward("x5 Enchanted Golden Apple", Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 5), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 5));
            }, 99),
            new Reward("Knight Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("Knight"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 100),
            new Reward("Mystic Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("Mystic"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 75),
            new Reward("Enchantment", Item::get(Item::ENCHANTED_BOOK, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
            }, 25),
            new Reward("x16 TNT", Item::get(Item::TNT, 0, 16), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::TNT, 0, 16));
            }, 89),
            new Reward("Sell Wand (Uses: 25)", Item::get(Item::DIAMOND_HOE, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new SellWand(25))->getItemForm());
            }, 50),
            new Reward("x2 Common Crate Keys", Item::get(Item::STRING), function(CrypticPlayer $player): void {
                $crate = Cryptic::getInstance()->getCrateManager()->getCrate("Common");
                $player->getSession()->addKeys($crate, 2);
            }, 36)
        ]);
    }

    /**
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function spawnTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle !== null) {
            return;
        }
        $player->addFloatingText(Position::fromObject($this->getPosition()->add(0.5, 1.25, 0.5), $this->getPosition()->getLevel()), $this->getName(), "§l§cVote Crate§r\n§7You have §c" . $player->getSession()->getKeys($this) . " §7keys!§r");
    }

    /**
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function updateTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle === null) {
            $this->spawnTo($player);
        }
        $text = $player->getFloatingText($this->getName());
        $text->update("§l§cVote Crate§r\n§7You have §c" . $player->getSession()->getKeys($this) . " §7keys!§r");
        $text->sendChangesTo($player);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function despawnTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle !== null) {
            $particle->despawn($player);
        }
    }

    /**
     * @param Reward        $reward
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function showReward(Reward $reward, CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle === null) {
            $this->spawnTo($player);
        }
        $text = $player->getFloatingText($this->getName());
        $text->update("§l§c" . $reward->getName());
        $text->sendChangesTo($player);
    }
}
