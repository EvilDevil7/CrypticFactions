<?php

namespace core\entity\forms;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\item\types\EnchantmentBook;
use core\item\types\EnchantmentRemover;
use core\CrypticPlayer;
use libs\form\ModalForm;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AlchemistConfirmationForm extends ModalForm{

	/**
	 * AlchemistConfirmationForm constructor.
	 *
	 * @param CrypticPlayer $player
	 */
	public function __construct(CrypticPlayer $player){
		$item = $player->getInventory()->getItemInHand();
		if($item->getId() !== Item::ENCHANTED_BOOK){
			return;
		}
		$tag = $item->getNamedTagEntry(CustomItem::CUSTOM);
		if(!$tag instanceof CompoundTag){
			return;
		}
		$enchantment = Enchantment::getEnchantment($tag->getInt(EnchantmentBook::ENCHANTMENT));
		$title = TextFormat::BOLD . TextFormat::AQUA . "Alchemist";
		$this->title = $title;
        $text = "Ah, yes. A {$enchantment->getName()} book, what I was looking for. I'll be willing to trade a random enchantment remover. Will you accept my offer?";
        $this->content = $text;
        $this->button1 = "gui.yes";
        $this->button2 = "gui.no";
        parent::__construct($title, $text);
    }

	/**
	 * @param Player $player
	 * @param bool   $choice
	 */
	public function onSubmit(Player $player, bool $choice) : void{
		if(!$player instanceof CrypticPlayer){
			return;
		}
		if($choice == true){
			$item = $player->getInventory()->getItemInHand();
			$player->getInventory()->removeItem($item);
			$item = new EnchantmentRemover(mt_rand(1, 100));
			$player->getInventory()->addItem($item->getItemForm());
			return;
		}
		return;
	}
}
