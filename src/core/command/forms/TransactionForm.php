<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\CrypticPlayer;
use core\price\event\ItemBuyEvent;
use core\price\event\ItemSellEvent;
use core\price\PriceEntry;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\CustomForm;
use libs\form\CustomFormResponse;
use libs\form\element\Label;
use libs\form\element\Slider;
use libs\form\element\Toggle;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TransactionForm extends CustomForm {

    /** @var PriceEntry */
    private $priceEntry;

    /**
     * TransactionForm constructor.
     *
     * @param CrypticPlayer $player
     * @param PriceEntry    $entry
     */
    public function __construct(CrypticPlayer $player, PriceEntry $entry) {
        $this->priceEntry = $entry;
        $title = TextFormat::BOLD . TextFormat::AQUA . $entry->getName();
        $elements = [];
        $message = TextFormat::GRAY . "Your balance: " . TextFormat::WHITE . "$" . number_format($player->getBalance());
        $elements[] = new Label("Balance", $message);
        $buyPrice = $entry->getBuyPrice();
        if($buyPrice === null) {
            $buyPrice = TextFormat::WHITE . "Not buyable";
        }
        else {
            $elements[] = new Toggle("Enable Buy", "Buy", true);
            $buyPrice = TextFormat::WHITE . number_format($buyPrice);
        }
        $elements[] = new Label("Buy Price", TextFormat::DARK_AQUA . "Unit buy price: " . $buyPrice);
        $sellPrice = $entry->getSellPrice();
        if($sellPrice === null) {
            $sellPrice = TextFormat::WHITE . "Not sellable";
        }
        else {
            $elements[] = new Toggle("Enable Sell", "Sell", false);
            $sellPrice = TextFormat::WHITE . number_format($sellPrice);
        }
        $elements[] = new Label("Sell Price", TextFormat::DARK_AQUA . "Unit sell price: " . $sellPrice);
        $elements[] = new Slider("Amount", "Amount", 1, 64);
        parent::__construct($title, $elements);
    }

    /**
     * @param Player $player
     * @param CustomFormResponse $data
     *
     * @throws TranslationException
     */
    public function onSubmit(Player $player, CustomFormResponse $data): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $buyToggle = null;
        $sellToggle = null;
        if(isset($data->getAll()["Enable Buy"])){
            $buyToggle = $data->getBool("Enable Buy");
        }
        if(isset($data->getAll()["Enable Sell"])){
            $sellToggle = $data->getBool("Enable Sell");
        }
        $all = $data->getAll();
        $amount = (int)$all["Amount"];
        $item = clone $this->priceEntry->getItem();
        if(!$item instanceof Item) {
            return;
        }
        if($this->priceEntry->getPermission() !== null and (!$player->hasPermission($this->priceEntry->getPermission()))) {
            $player->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if($buyToggle !==null and $sellToggle !== null and $buyToggle === true and $sellToggle === true) {
            $player->sendMessage(Translation::getMessage("turnOnAToggle"));
            return;
        }
        if($buyToggle !==null and $sellToggle !== null and $buyToggle === false and $sellToggle === false) {
            $player->sendMessage(Translation::getMessage("turnOnAToggle"));
            return;
        }
        if($sellToggle !== null and $sellToggle === true and $this->priceEntry->getSellPrice() === null) {
            $player->sendMessage(Translation::getMessage("notSellable"));
            return;
        }
        if($buyToggle !== null and $buyToggle === true and $this->priceEntry->getBuyPrice() === null) {
            $player->sendMessage(Translation::getMessage("notBuyable"));
            return;
        }
        if($amount <= 0 or (!is_numeric($amount))) {
            $player->sendMessage(Translation::getMessage("invalidAmount"));
            return;
        }
        $inventory = $player->getInventory();
        if($buyToggle !== null and $buyToggle === true) {
            $price = $this->priceEntry->getBuyPrice() * $amount;
            $balance = $player->getBalance();
            if($price > $balance) {
                $player->sendMessage(Translation::getMessage("turnOnAToggle"));
                return;
            }
            $item->setCount($amount * $this->priceEntry->getItem()->getCount());
            $inventory->addItem($item);
            $player->subtractFromBalance($price);
            $player->sendMessage(Translation::getMessage("buy", [
                "amount" => TextFormat::GREEN . $item->getCount(),
                "item" => TextFormat::DARK_GREEN . $this->priceEntry->getName(),
                "price" => TextFormat::LIGHT_PURPLE . "$" . $price,
            ]));
            $event = new ItemBuyEvent($player, $item, $price);
            $event->call();
            return;
        }
        if($sellToggle !== null and $sellToggle === true) {
            $price = $this->priceEntry->getSellPrice() * $amount;
            $item->setCount($amount * $this->priceEntry->getItem()->getCount());
            if(!$inventory->contains($item)) {
                $player->sendMessage(Translation::getMessage("nothingSellable"));
                return;
            }
            $inventory->removeItem($item);
            $player->addToBalance($price);
            $player->sendMessage(Translation::getMessage("sell", [
                "amount" => TextFormat::GREEN . $item->getCount(),
                "item" => TextFormat::DARK_GREEN . $this->priceEntry->getName(),
                "price" => TextFormat::LIGHT_PURPLE . "$" . number_format($price),
            ]));
            $event = new ItemSellEvent($player, $item, $price);
            $event->call();
            return;
        }
        return;
    }
}