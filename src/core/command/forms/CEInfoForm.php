<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\item\enchantment\Enchantment;
use core\item\ItemManager;
use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\utils\TextFormat;

class CEInfoForm extends CustomForm {

    /**
     * CEInfoForm constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Enchantments";
        $elements = [];
        $enchantments = [];
        foreach(ItemManager::getEnchantments() as $enchantment) {
            if($enchantment instanceof Enchantment and (!in_array($enchantment->getId(), $enchantments))) {
                $enchantments[] = $enchantment->getId();
                $elements[] = new Label($enchantment->getName(), ItemManager::rarityToColor($enchantment->getRarity()) . TextFormat::BOLD . $enchantment->getName() . TextFormat::RESET . TextFormat::AQUA . "\nApplicable Items: " . TextFormat::WHITE . ItemManager::flagToString($enchantment->getPrimaryItemFlags()) . TextFormat::AQUA . "\nMax Level: " . TextFormat::WHITE . $enchantment->getMaxLevel()  . TextFormat::AQUA . "\nDescription: " . TextFormat::WHITE . $enchantment->getDescription()  . TextFormat::AQUA . "\nRarity: " . TextFormat::WHITE . ItemManager::rarityToString($enchantment->getRarity()));
            }
        }
        parent::__construct($title, $elements);
    }
}