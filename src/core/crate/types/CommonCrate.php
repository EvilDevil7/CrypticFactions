<?php

declare(strict_types = 1);

namespace core\crate\types;

use core\crate\Crate;
use core\crate\Reward;
use core\item\types\ChestKit;
use core\item\types\SellWand;
use core\item\types\XPNote;
use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\UtilsException;
use pocketmine\item\Item;
use pocketmine\level\Position;

class CommonCrate extends Crate {

    /**
     * CommonCrate constructor.
     *
     * @param Position $position
     */
    public function __construct(Position $position) {
        parent::__construct(self::COMMON, $position, [
            new Reward("$1,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(1000);
            }, 50),
            new Reward("$2,500", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(2000);
            }, 50),
            new Reward("1,000 XP Note", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new XPNote(1000))->getItemForm());
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
            new Reward("Knight Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("Knight"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 100),
            new Reward("Wizard Kit", Item::get(Item::CHEST_MINECART, 0, 1), function(CrypticPlayer $player): void {
                $item = new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("Wizard"));
                $player->getInventory()->addItem($item->getItemForm());
            }, 75),
            new Reward("Sell Wand (Uses: 5)", Item::get(Item::DIAMOND_HOE, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new SellWand(5))->getItemForm());
            }, 89)
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
        $player->addFloatingText(Position::fromObject($this->getPosition()->add(0.5, 1.25, 0.5), $this->getPosition()->getLevel()), $this->getName(), "§l§aCommon Crate§r\n§7You have §a" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§aCommon Crate§r\n§7You have §a" . $player->getSession()->getKeys($this) . " §7keys!§r");
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
        $text->update("§l§a" . $reward->getName());
        $text->sendChangesTo($player);
    }
}
