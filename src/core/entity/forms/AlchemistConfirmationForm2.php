<?php

namespace core\entity\forms;

use core\item\CustomItem;
use core\item\enchantment\Enchantment;
use core\item\forms\AlchemistForm;
use core\item\ItemManager;
use core\item\types\EnchantmentRemover;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\ModalForm;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AlchemistConfirmationForm2 extends ModalForm {

    /**
     * AlchemistConfirmationForm2 constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Alchemist";
        $this->title = $title;
        $text = "Ok, I guess I will do this since you got an enchantment remover. The risk is high. A random enchantment could be removed. Are you willing to take the risk?";
		$this->content = $text;
		$this->button1 = "gui.yes";
		$this->button2 = "gui.no";
        parent::__construct($title, $text);
    }

    /**
     * @param Player $player
     * @param bool $choice
     *
     * @throws TranslationException
     */
    public function onSubmit(Player $player, bool $choice): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($choice == true) {
            foreach($player->getInventory()->getContents() as $i) {
                $tag = $i->getNamedTagEntry(CustomItem::CUSTOM);
                if($tag instanceof CompoundTag  and $tag->hasTag(EnchantmentRemover::SUCCESS_PERCENTAGE, IntTag::class) and $i->getId() === Item::SUGAR) {
                    $player->getInventory()->removeItem($i);
                    $success = $tag->getInt(EnchantmentRemover::SUCCESS_PERCENTAGE);
                    break;
                }
            }
            $item = $player->getInventory()->getItemInHand();
            if(mt_rand(1, 100) <= $success) {
                $player->sendForm(new AlchemistForm( $item, $i));
            }
            else {
                $player->getInventory()->removeItem($item);
                $enchantments = $item->getEnchantments();
                $enchantment = $enchantments[array_rand($enchantments)];
                $item->removeEnchantment($enchantment->getId(), $enchantment->getLevel());
                $lore = [];
                foreach($item->getEnchantments() as $e) {
                    if($e->getType() instanceof Enchantment) {
                        $lore[] = TextFormat::RESET . ItemManager::rarityToColor($e->getType()->getRarity()) . $e->getType()->getName() . " " . ItemManager::getRomanNumber($e->getLevel());
                    }
                }
                $item->setLore($lore);
                $player->getInventory()->addItem($item);
                $player->sendMessage(Translation::getMessage("enchantmentRemoverFail", [
                    "enchantment" => TextFormat::RED . $enchantment->getType()->getName()
                ]));
                $player->getLevel()->addSound(new AnvilBreakSound($player));
                return;
            }
        }
        return;
    }
}
