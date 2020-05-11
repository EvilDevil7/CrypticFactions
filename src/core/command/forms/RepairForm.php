<?php

namespace core\command\forms;

use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\ModalForm;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RepairForm extends ModalForm {

    /** @var int */
    private $cost;

    /**
     * RepairForm constructor.
     *
     * @param Player $player
     */
    public function __construct(Player $player) {
        $item = $player->getInventory()->getItemInHand();
        $levels = 0;
        foreach($item->getEnchantments() as $enchantment) {
            $levels = $levels + $enchantment->getLevel();
        }
        $damage = $item->getDamage();
        if($levels == 0) {
            $cost = $damage * 5;
        }
        else {
            $factor = $levels * 2;
            $cost = $item->getDamage() * $factor;
        }
        $this->cost = $cost;
        $title = TextFormat::BOLD . TextFormat::AQUA . "Repair";
        $text = "Would you like to repair the item you currently are holding? The cost will be $$cost.";
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
            if($player->getBalance() >= $this->cost) {
                $item = $player->getInventory()->getItemInHand();
                $player->getInventory()->setItemInHand($item->setDamage(0));
                $player->subtractFromBalance($this->cost);
                $player->sendMessage(Translation::getMessage("successRepair"));
                $player->getLevel()->addSound(new AnvilUseSound($player));
                return;
            }
            $player->sendMessage(Translation::getMessage("notEnoughMoney"));
            return;
        }
        return;
    }
}
