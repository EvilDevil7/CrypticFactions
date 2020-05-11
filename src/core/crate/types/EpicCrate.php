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
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;

class EpicCrate extends Crate {

    /**
     * EpicCrate constructor.
     * @param Position $position
     */
    public function __construct(Position $position) {
        parent::__construct(self::EPIC, $position, [
            new Reward("$10,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(10000);
            }, 50),
            new Reward("$20,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(20000);
            }, 50),
            new Reward("$50,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(50000);
            }, 50),
            new Reward("5,000 XP Note", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new XPNote(5000))->getItemForm());
            }, 100),
            new Reward("Blaze Spawner", Item::get(Item::MOB_HEAD_BLOCK, 0, 1), function(CrypticPlayer $player): void {
                $item = Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::BLAZE)
                ]));
                $item->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Blaze Spawner");
                $player->getInventory()->addItem($item);
                }, 5),
            new Reward("x64 Obsidian", Item::get(Item::OBSIDIAN, 0, 64), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::OBSIDIAN, 0, 64));
            }, 89),
            new Reward("x32 Enchanted Golden Apple", Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 32));
            }, 79),
            new Reward("God Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("God"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 85),
            new Reward("Cryptic Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("Cryptic"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 65),
            new Reward("Enchantment", Item::get(Item::ENCHANTED_BOOK, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
            }, 100),
            new Reward("x16 TNT", Item::get(Item::TNT, 0, 16), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::TNT, 0, 16));
            }, 87),
            new Reward("Sell Wand (Uses: 25)", Item::get(Item::DIAMOND_HOE, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new SellWand(25))->getItemForm());
            }, 90),
            new Reward("x32 Bedrock", Item::get(Item::BEDROCK, 0, 32), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem(Item::get(Item::BEDROCK, 0, 32));
            }, 87),
            new Reward("x2 Legendary Crate Keys", Item::get(Item::PAPER), function(CrypticPlayer $player): void {
                $crate = Cryptic::getInstance()->getCrateManager()->getCrate("Legendary");
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
        $player->addFloatingText(Position::fromObject($this->getPosition()->add(0.5, 1.25, 0.5), $this->getPosition()->getLevel()), $this->getName(), "§l§5Epic Crate§r\n§7You have §5" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§5Epic Crate§r\n§7You have §5" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§5" . $reward->getName());
        $text->sendChangesTo($player);
    }
}
