<?php

namespace core\combat\boss\types;

use core\combat\boss\Boss;
use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\item\types\LuckyBlock;
use core\item\types\MoneyNote;
use core\item\types\SacredStone;
use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\Utils;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Witcher extends Boss {

    const BOSS_ID = 3;

    /**
     * Witcher constructor.
     * @param Level $level
     * @param CompoundTag $nbt
     */
    public function __construct(Level $level, CompoundTag $nbt) {
        $path = Cryptic::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "witcher.png";
        $this->setSkin(Utils::createSkin(Utils::getSkinDataFromPNG($path)));
        parent::__construct($level, $nbt);
        $this->setMaxHealth(1000);
        $this->setHealth(1000);
        $this->setNametag(TextFormat::BOLD . TextFormat::YELLOW . "Witcher " . TextFormat::RESET . TextFormat::WHITE . $this->getHealth() . "/" . $this->getMaxHealth());
        $this->setScale(2.1);
        $this->attackDamage = 230;
        $this->speed = 1;
        $this->attackWait = 5;
        $this->regenerationRate = 10;
    }

    /**
     * @param int $tickDiff
     *
     * @return bool
     */
    public function entityBaseTick(int $tickDiff = 1): bool {
        $this->setNametag(TextFormat::BOLD . TextFormat::YELLOW . "Witcher " . TextFormat::RESET . TextFormat::WHITE . $this->getHealth() . "/" . $this->getMaxHealth());
        return parent::entityBaseTick($tickDiff);
    }

    public function onDeath(): void {
        $rewards = [
            (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
            (new LuckyBlock(100))->getItemForm(),
            (new SacredStone())->getItemForm()->setCount(mt_rand(1, 4)),
            (new MoneyNote(mt_rand(100000, 250000)))->getItemForm()
        ];
        $d = null;
        $p = null;
        foreach($this->damages as $player => $damage) {
            if(Server::getInstance()->getPlayer($player) === null) {
                continue;
            }
            $online = Server::getInstance()->getPlayer($player);
            if($damage > $d) {
                $d = $damage;
                $p = $online;
            }
        }
        if($p === null) {
            return;
        }
        //top
        $keys = ["Legendary" => 1, "Epic" => 2, "Rare" => 5];
        $rand = array_rand($keys);
        $crate = Cryptic::getInstance()->getCrateManager()->getCrate($rand);
        if($p instanceof CrypticPlayer){
            $p->getSession()->addKeys($crate, $keys[$rand]);
        }

        Server::getInstance()->broadcastMessage($p->getDisplayName() . TextFormat::GRAY . " has dealt the most damage " . TextFormat::DARK_GRAY . "(" . TextFormat::WHITE . $d . TextFormat::RED . TextFormat::BOLD . " DMG" . TextFormat::RESET . TextFormat::DARK_GRAY . ")" . TextFormat::GRAY . " to " . TextFormat::BOLD . TextFormat::YELLOW . "Witcher " . TextFormat::RESET . TextFormat::GRAY . "and received:");
        foreach($rewards as $item) {
            $name = TextFormat::RESET . TextFormat::WHITE . $item->getName();
            if($item->hasCustomName()) {
                $name = $item->getCustomName();
            }
            Server::getInstance()->broadcastMessage($name . TextFormat::RESET . TextFormat::GRAY . " * " . TextFormat::WHITE . $item->getCount());
            if($p->getInventory()->canAddItem($item)) {
                $p->getInventory()->addItem($item);
                continue;
            }
            $p->getLevel()->dropItem($p, $item);
        }


        //other
        foreach($this->damages as $player => $damage) {
            if($player === $p->getName()) {
                continue;
            }
            if(Server::getInstance()->getPlayer($player) === null) {
                continue;
            }
            $online = Server::getInstance()->getPlayer($player);
            $online->sendMessage(TextFormat::GRAY . "You dealt " . TextFormat::WHITE . $damage . TextFormat::RED . TextFormat::BOLD . " DMG" . TextFormat::GRAY . " to " . TextFormat::BOLD . TextFormat::YELLOW . "Witcher " . TextFormat::RESET . TextFormat::GRAY . "and received:");
            $rewards = [
                (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(),
                (new LuckyBlock(100))->getItemForm(),
                (new MoneyNote(mt_rand(20000, 50000)))->getItemForm()
            ];
            if($p instanceof CrypticPlayer){
                $crate = Cryptic::getInstance()->getCrateManager()->getCrate("Rare");
                $p->getSession()->addKeys($crate, 2);
            }
            foreach($rewards as $item) {
                $name = TextFormat::RESET . TextFormat::WHITE . $item->getName();
                if($item->hasCustomName()) {
                    $name = $item->getCustomName();
                }
                $online->sendMessage($name . TextFormat::RESET . TextFormat::GRAY . " * " . TextFormat::WHITE . $item->getCount());
                if($online->getInventory()->canAddItem($item)) {
                    $online->getInventory()->addItem($item);
                    continue;
                }
                $online->getLevel()->dropItem($online, $item);
            }
        }
    }
}