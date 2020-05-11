<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\price\event\ItemSellEvent;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class SellCommand extends Command {

    /**
     * RewardsCommand constructor.
     */
    public function __construct() {
        parent::__construct("sell", "Sell items", "/sell <hand/all/auto>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof CrypticPlayer) {
            $inventory = $sender->getInventory();
            if(!isset($args[0])) {
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                return;
            }
            $sellables = $this->getCore()->getPriceManager()->getSellables();
            switch($args[0]) {
                case "hand":
                    $item = $inventory->getItemInHand();
                    $sellable = false;
                    $entry = null;
                    if(isset($sellables[$item->getId()])) {
                        $entry = $sellables[$item->getId()];
                        if($entry->equal($item)) {
                            $sellable = true;
                        }
                    }
                    if($sellable === false) {
                        $sender->sendMessage(Translation::getMessage("nothingSellable"));
                        return;
                    }
                    $count = $item->getCount();
                    $price = $count * $entry->getSellPrice();
                    $inventory->removeItem($item);
                    $sender->addToBalance($price);
                    $event = new ItemSellEvent($sender, $item, $price);
                    $event->call();
                    $item = $entry->getName();
                    $sender->sendMessage(Translation::getMessage("sell", [
                        "amount" => TextFormat::GREEN . $count,
                        "item" => TextFormat::DARK_GREEN . $item,
                        "price" => TextFormat::LIGHT_PURPLE . "$" . $price,
                    ]));
                    return;
                    break;
                case "all":
                    $content = $sender->getInventory()->getContents();
                    /** @var Item[] $items */
                    $items = [];
                    $sellable = false;
                    $entries = [];
                    foreach($content as $item) {
                        if(!isset($sellables[$item->getId()])) {
                            continue;
                        }
                        $entry = $sellables[$item->getId()];
                        if(!$entry->equal($item)) {
                            continue;
                        }
                        if($sellable === false) {
                            $sellable = true;
                        }
                        if(!isset($entries[$entry->getName()])) {
                            $entries[$entry->getName()] = $entry;
                            $items[$entry->getName()] = $item;
                        }
                        else {
                            $items[$entry->getName()]->setCount($items[$entry->getName()]->getCount() + $item->getCount());
                        }
                    }
                    if($sellable === false) {
                        $sender->sendMessage(Translation::getMessage("nothingSellable"));
                        return;
                    }
                    $price = 0;
                    foreach($entries as $entry) {
                        $item = $items[$entry->getName()];
                        $price += $item->getCount() * $entry->getSellPrice();
                        $inventory->removeItem($item);
                        $event = new ItemSellEvent($sender, $item, $price);
                        $event->call();
                        $sender->sendMessage(Translation::getMessage("sell", [
                            "amount" => TextFormat::GREEN . $item->getCount(),
                            "item" => TextFormat::DARK_GREEN . $entry->getName(),
                            "price" => TextFormat::LIGHT_PURPLE . "$" . $price
                        ]));
                    }
                    $sender->addToBalance($price);
                    return;
                    break;
                case "auto":
                    if($sender->hasPermission("permission.tier3")) {
                        $sender->setAutoSelling(!$sender->isAutoSelling());
                        $sender->sendMessage(Translation::getMessage("autoSellToggle"));
                        return;
                    }
                    $sender->sendMessage(Translation::getMessage("noPermission"));
                    break;
                default:
                    $sender->sendMessage(Translation::getMessage("usageMessage", [
                        "usage" => $this->getUsage()
                    ]));
            }
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}