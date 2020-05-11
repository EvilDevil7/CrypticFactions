<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\Cryptic;
use core\CrypticPlayer;
use core\price\ShopPlace;
use libs\form\MenuForm;
use libs\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShopForm extends MenuForm {

    /**
     * ShopForm constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Shop Menu";
        $text = "Select where you would like to shop.";
        $options = [];
        $options[] = new MenuOption("Enchantments");
        /** @var ShopPlace $place */
        foreach(Cryptic::getInstance()->getPriceManager()->getPlaces() as $place) {
            $options[] = new MenuOption($place->getName());
        }
        parent::__construct($title, $text, $options);
    }

    /**
     * @param Player $player
     * @param int $selectedOption
     */
    public function onSubmit(Player $player, int $selectedOption): void {
        $option = $this->getOption($selectedOption);
        $text = $option->getText();
        if($text === "Enchantments" and $player instanceof CrypticPlayer) {
            $player->sendForm(new EnchantmentShopForm($player));
            return;
        }
        $player->sendForm(new ItemListForm(Cryptic::getInstance()->getPriceManager()->getPlace($text)));
    }
}
