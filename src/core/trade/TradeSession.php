<?php

declare(strict_types = 1);

namespace core\trade;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use core\libs\muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TradeSession {

    /** @var CrypticPlayer */
    private $sender;

    /** @var CrypticPlayer */
    private $receiver;

    /** @var int */
    private $time;

    /** @var null|int */
    private $tradeTime = null;

    /** @var bool */
    private $senderStatus = false;

    /** @var bool */
    private $receiverStatus = false;

    /** @var InvMenu */
    private $menu;

    /** @var int */
    private $key;

    /**
     * TradeSession constructor.
     *
     * @param CrypticPlayer $sender
     * @param CrypticPlayer $receiver
     */
    public function __construct(CrypticPlayer $sender, CrypticPlayer $receiver) {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->time = time();
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->setName(TextFormat::YELLOW . "Trading Session");
        $item = Item::get(Item::STAINED_GLASS, 14);
        $item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
        $this->menu->getInventory()->setItem(4, $item);
        $item = Item::get(Item::STAINED_GLASS, 14);
        $item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
        $this->menu->getInventory()->setItem(22, $item);
        $this->menu->setListener(
            function(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action): bool {
                if($action->getSlot() === 4 and $player->getRawUniqueId() === $this->sender->getRawUniqueId()) {
                    if($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 14) {
                        $item = Item::get(Item::STAINED_GLASS, 13);
                        $item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
                        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
                        $action->getInventory()->setItem(4, $item);
                        $this->senderStatus = true;
                        return false;
                    }
                    elseif($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 13) {
                        $item = Item::get(Item::STAINED_GLASS, 14);
                        $item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
                        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->sender->getName()]);
                        $action->getInventory()->setItem(4, $item);
                        $this->senderStatus = false;
                        return false;
                    }
                }
                if($action->getSlot() === 22 and $player->getRawUniqueId() === $this->receiver->getRawUniqueId()) {
                    if($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 14) {
                        $item = Item::get(Item::STAINED_GLASS, 13);
                        $item->setCustomName(TextFormat::RESET . TextFormat::GREEN . TextFormat::BOLD . "ACCEPT");
                        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
                        $action->getInventory()->setItem(22, $item);
                        $this->receiverStatus = true;
                        return false;
                    }
                    elseif($itemClicked->getId() === Item::STAINED_GLASS and $itemClicked->getDamage() === 13) {
                        $item = Item::get(Item::STAINED_GLASS, 14);
                        $item->setCustomName(TextFormat::RESET . TextFormat::RED . TextFormat::BOLD . "DENY");
                        $item->setLore([TextFormat::RESET . TextFormat::GRAY . "This can only be modified by " . TextFormat::LIGHT_PURPLE . $this->receiver->getName()]);
                        $action->getInventory()->setItem(22, $item);
                        $this->receiverStatus = false;
                        return false;
                    }
                }
                if($action->getSlot() === 13) {
                    return false;
                }
                if(($action->getSlot() % 9) < 4 and $player->getRawUniqueId() === $this->sender->getRawUniqueId() and $this->senderStatus === false and $this->receiverStatus === false) {
                    return true;
                }
                if(($action->getSlot() % 9) > 4 and $player->getRawUniqueId() === $this->receiver->getRawUniqueId() and $this->receiverStatus === false and $this->senderStatus === false) {
                    return true;
                }
                return false;
            }
        );
        $this->menu->setInventoryCloseListener(
            function(Player $player, Inventory $inventory): void {
                foreach($this->menu->getInventory()->getContents() as $slot => $item) {
                    if(($slot % 9) < 4) {
                        if($this->sender->isOnline()) {
                            $inventory = $this->sender->getInventory();
                            if($inventory->canAddItem($item)) {
                                $inventory->addItem($item);
                                continue;
                            }
                            $this->sender->getLevel()->dropItem($this->sender, $item);
                        }
                        else {
                            if(!$this->sender->getInventory()->canAddItem($item)){
                                $this->sender->addToInbox($item);
                                $this->sender->sendMessage(TextFormat::RED . "Your inventory is full adding item to /inbox");
                            }
                            $this->sender->getInventory()->addItem($item);
                        }
                    }
                }
                foreach($this->menu->getInventory()->getContents() as $slot => $item) {
                    if(($slot % 9) > 4) {
                        if($this->receiver->isOnline()) {
                            $inventory = $this->receiver->getInventory();
                            if($inventory->canAddItem($item)) {
                                $inventory->addItem($item);
                                continue;
                            }
                            $this->receiver->getLevel()->dropItem($this->receiver, $item);
                        }
                        else {
                            if(!$this->receiver->getInventory()->canAddItem($item)){
                                $this->receiver->addToInbox($item);
                                $this->receiver->sendMessage(TextFormat::RED . "Your inventory is full adding item to /inbox");
                            }
                            $this->receiver->getInventory()->addItem($item);
                        }
                    }
                }
                $this->menu->getInventory()->clearAll(true);
                if($this->sender->isOnline()) {
                    $this->sender->removeWindow($this->menu->getInventory(), true);
                }
                if($this->receiver->isOnline()) {
                    $this->receiver->removeWindow($this->menu->getInventory(), true);
                }
                Cryptic::getInstance()->getTradeManager()->removeSession($this->key);
            }
        );
    }

    /**
     * @return CrypticPlayer
     */
    public function getSender(): CrypticPlayer {
        return $this->sender;
    }

    /**
     * @return CrypticPlayer
     */
    public function getReceiver(): CrypticPlayer {
        return $this->receiver;
    }

    public function sendMenus() {
        $this->menu->send($this->sender);
        $this->menu->send($this->receiver);
    }

    /**
     * @param int $key
     * @param TradeManager $manager
     *
     * @throws TranslationException
     */
    public function tick(int $key, TradeManager $manager): void {
        $this->key = $key;
        if($this->senderStatus === true and $this->receiverStatus === true) {
            if($this->tradeTime === null) {
                $this->tradeTime = time();
            }
        }
        else {
            $this->tradeTime = null;
        }
        if($this->tradeTime !== null) {
            $time = 5 - (time() - $this->tradeTime);
            $this->menu->getInventory()->setItem(13, Item::get(Item::STAINED_GLASS, 0, $time)->setCustomName(TextFormat::RESET . TextFormat::GRAY . "Trade in $time seconds"));
            if($time <= 1) {
                foreach($this->menu->getInventory()->getContents() as $slot => $item) {
                    if(($slot % 9) < 4) {
                        if(!$this->receiver->getInventory()->canAddItem($item)){
                            $this->receiver->addToInbox($item);
                            $this->receiver->sendMessage(TextFormat::RED . "Your inventory is full adding item to /inbox");
                        }
                        $this->receiver->getInventory()->addItem($item);
                    }
                }
                foreach($this->menu->getInventory()->getContents() as $slot => $item) {
                    if(($slot % 9) > 4) {
                        if(!$this->sender->getInventory()->canAddItem($item)){
                            $this->sender->addToInbox($item);
                            $this->sender->sendMessage(TextFormat::RED . "Your inventory is full adding item to /inbox");
                        }
                        $this->sender->getInventory()->addItem($item);
                    }
                }
                $this->menu->getInventory()->clearAll(true);
                if($this->sender->isOnline()) {
                    $this->sender->removeWindow($this->menu->getInventory(), true);
                    $this->sender->sendMessage(Translation::getMessage("successTrade"));
                }
                if($this->receiver->isOnline()) {
                    $this->receiver->removeWindow($this->menu->getInventory(), true);
                    $this->receiver->sendMessage(Translation::getMessage("successTrade"));
                }
                $manager->removeSession($key);
            }
        }
    }
}
