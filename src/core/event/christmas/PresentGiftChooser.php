<?php

declare(strict_types = 1);

namespace core\event\christmas;

use core\crate\Reward;
use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\item\types\HolyBox;
use core\item\types\LuckyBlock;
use core\item\types\SacredStone;
use core\item\types\SellWand;
use core\Cryptic;
use core\CrypticPlayer;
use core\rank\Rank;
use pocketmine\item\Item;

class PresentGiftChooser {

    /** @var Reward[] */
    private $rewards = [];

    /**
     * Crate constructor.
     */
    public function __construct() {
        $this->rewards = [
            new Reward("Lucky Block", Item::get(Item::NAME_TAG, 0, 1), function(CrypticPlayer $player): void {
                $item = (new LuckyBlock(mt_rand(75, 100)))->getItemForm();
                $player->getInventory()->addItem($item);
            }, 1000),
            new Reward("Sacred Stone", Item::get(Item::NETHER_QUARTZ, 0, 1), function(CrypticPlayer $player): void {
                $item = (new SacredStone())->getItemForm();
                $player->getInventory()->addItem($item);
            }, 100),
            new Reward("Holy Box", Item::get(Item::CHEST, 0, 1), function(CrypticPlayer $player): void {
                $kits = Cryptic::getInstance()->getKitManager()->getSacredKits();
                $kit = $kits[array_rand($kits)];
                $item = (new HolyBox($kit))->getItemForm();
                $player->getInventory()->addItem($item);
            }, 10),
            new Reward( "$100,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(100000);
            }, 750),
            new Reward("$1,000,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(1000000);
            }, 100),
            new Reward("$10,000,000", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->addToBalance(10000000);
            }, 10),
            new Reward("God Rank", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->setRank($player->getCore()->getRankManager()->getRankByIdentifier(Rank::GOD));
            }, 1),
            new Reward("Warlord Rank", Item::get(Item::PAPER, 0, 1), function(CrypticPlayer $player): void {
                $player->setRank($player->getCore()->getRankManager()->getRankByIdentifier(Rank::WARLORD));
            }, 1),
            new Reward("Sell Wand", Item::get(Item::DIAMOND_HOE, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new SellWand(50))->getItemForm());
            }, 800),
            new Reward("Enchantment", Item::get(Item::ENCHANTED_BOOK, 0, 1), function(CrypticPlayer $player): void {
                $player->getInventory()->addItem((new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
            }, 750)
        ];
    }

    /**
     * @return Reward[]
     */
    public function getRewards(): array {
        return $this->rewards;
    }

    /**
     * @param int $loop
     *
     * @return Reward
     */
    public function getReward(int $loop = 0): Reward {
        $chance = mt_rand(0, 1000);
        $reward = $this->rewards[array_rand($this->rewards)];
        if($loop >= 20) {
            return $reward;
        }
        if($reward->getChance() <= $chance) {
            return $this->getReward($loop + 1);
        }
        return $reward;
    }
}
