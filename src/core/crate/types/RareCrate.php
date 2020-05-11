<?php

declare(strict_types = 1);

namespace core\crate\types;

use core\crate\Crate;
use core\crate\Reward;
use core\item\ItemManager;
use core\item\types\ChestKit;
use core\item\types\EnchantmentBook;
use core\item\types\SellWand;
use core\item\types\XPNote;
use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\UtilsException;
use pocketmine\item\Item;
use pocketmine\level\Position;

class RareCrate extends Crate {

    /**
     * RareCrate constructor.
     * @param Position $position
     */
    public function __construct(Position $position) {
        parent::__construct(self::RARE, $position, [
            new Reward("$5,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(5000);
            }, 50),
            new Reward("$10,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(10000);
            }, 50),
            new Reward("3,000 XP Note", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new XPNote(3000))->getItemForm());
            }, 100),
            new Reward("x32 Obsidian", Item::get(Item::OBSIDIAN, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::OBSIDIAN, 0, 32));
            }, 85),
            new Reward("x5 Enchanted Golden Apple", Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 5), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 5));
            }, 79),
            new Reward("x32 Golden Apple", Item::get(Item::GOLDEN_APPLE, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::GOLDEN_APPLE, 0, 32));
            }, 99),
            new Reward("Enchantment", Item::get(Item::ENCHANTED_BOOK, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
            }, 100),
            new Reward("King Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("King"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 100),
            new Reward("God Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("God"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 75),
            new Reward("x32 TNT", Item::get(Item::TNT, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::TNT, 0, 32));
            }, 91),
            new Reward("Sell Wand (Uses: 10)", Item::get(Item::DIAMOND_HOE, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new SellWand(10))->getItemForm());
            }, 90),
            new Reward("x10 Bedrock", Item::get(Item::BEDROCK, 0, 10), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::BEDROCK, 0, 10));
            }, 91),
            new Reward("x2 Epic Crate Keys", Item::get(Item::STRING), function(CrypticPlayer $player): void {
                $crate = Cryptic::getInstance()->getCrateManager()->getCrate("Epic");
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
        $player->addFloatingText(Position::fromObject($this->getPosition()->add(0.5, 1.25, 0.5), $this->getPosition()->getLevel()), $this->getName(), "§l§bRare Crate§r\n§7You have §b" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§bRare Crate§r\n§7You have §b" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§b" . $reward->getName());
        $text->sendChangesTo($player);
    }
}
