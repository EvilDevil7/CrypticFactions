<?php

namespace core\item\forms;

use core\item\enchantment\Enchantment;
use core\item\ItemManager;
use libs\form\MenuForm;
use libs\form\MenuOption;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class AlchemistForm extends MenuForm {

    /** @var Item */
    private $item;

    /** @var Item */
    private $enchantmentRemover;

    /**
     * AlchemistForm constructor.
     *
     * @param Item $item
     * @param Item $enchantmentRemover
     */
    public function __construct(Item $item, Item $enchantmentRemover) {
        $this->item = $item;
        $this->enchantmentRemover = $enchantmentRemover;
        $options = [];
        foreach($item->getEnchantments() as $enchantment) {
            $options[] = new MenuOption($enchantment->getType()->getName());
        }
        parent::__construct(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "Alchemist", TextFormat::WHITE . "Which enchantment would you like to remove?", $options);
    }

    /**
     * @param Player $player
     * @param int $selectedOption
     */
    public function onSubmit(Player $player, int $selectedOption): void {
        $inventory = $player->getInventory();
        $inventory->removeItem($this->item);
        $name = $this->getOption($selectedOption)->getText();
        $enchantment = Enchantment::getEnchantmentByName($name);
        if($enchantment == null){
            $enchantment = ItemManager::getEnchantment($name);
        }
        $this->item->removeEnchantment($enchantment->getId(), $this->item->getEnchantmentLevel($enchantment->getId()));
        $lore = [];
        foreach($this->item->getEnchantments() as $enchantment) {
            if($enchantment->getType() instanceof Enchantment) {
                $lore[] = TextFormat::RESET . ItemManager::rarityToColor($enchantment->getType()->getRarity()) . $enchantment->getType()->getName() . " " . ItemManager::getRomanNumber($enchantment->getLevel());
            }
        }
        $inventory->removeItem($this->enchantmentRemover);
        $this->item->setLore($lore);
        $inventory->addItem($this->item);
        $player->getLevel()->addSound(new AnvilBreakSound($player));
    }
}
