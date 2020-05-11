<?php

namespace core\command\forms;

use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\CustomForm;
use libs\form\CustomFormResponse;
use libs\form\element\Input;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class RenameItemForm extends CustomForm {

    /**
     * RenameItemForm constructor.
     */
    public function __construct() {
        $elements = [];
        $title = TextFormat::BOLD . TextFormat::AQUA . "Rename";
        $text = "What would you like to rename your item?";
        $elements[] = new Input("CustomName", $text);
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
        $value = $data->getString("CustomName");
        $name = str_replace("&", TextFormat::ESCAPE, $value);
        $cost = (int)strlen($name) * 100;
        if($player->getBalance() >= $cost) {
            $item = $player->getInventory()->getItemInHand();
            $item->setCustomName($name);
            $player->getInventory()->setItemInHand($item);
            $player->subtractFromBalance($cost);
            $player->sendMessage(Translation::getMessage("successRename"));
            $player->getLevel()->addSound(new AnvilUseSound($player));
            return;
        }
        $player->sendMessage(Translation::getMessage("notEnoughMoney"));
        return;
    }
}