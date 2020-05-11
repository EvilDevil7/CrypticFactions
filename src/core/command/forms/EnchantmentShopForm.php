<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\item\enchantment\Enchantment;
use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\item\types\HolyBox;
use core\item\types\MoneyNote;
use core\item\types\SacredStone;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\MenuForm;
use libs\form\MenuOption;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EnchantmentShopForm extends MenuForm {

    /**
     * EnchantmentShopForm constructor.
     *
     * @param CrypticPlayer $player
     */
    public function __construct(CrypticPlayer $player) {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Enchantment Shop";
        $text = "XP Levels: " . $player->getXpLevel();
        $options = [];
        $options[] = new MenuOption("Random Common Enchantment (30 XP Levels)");
        $options[] = new MenuOption("Random Uncommon Enchantment (50 XP Levels)");
        $options[] = new MenuOption("Random Rare Enchantment (80 XP Levels)");
        $options[] = new MenuOption("Random Legendary Enchantment (110 XP Levels)");
        parent::__construct($title, $text, $options);
    }

    /**
     * @param Player $player
     * @param int $selectedOption
     *
     * @throws TranslationException
     */
    public function onSubmit(Player $player, int $selectedOption): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $option = $this->getOption($selectedOption);
        if($player->getInventory()->getSize() === count($player->getInventory()->getContents())) {
            $player->sendMessage(Translation::getMessage("fullInventory"));
            return;
        }
        switch($option->getText()) {
            case "Random Common Enchantment (30 XP Levels)":
                $levels = 30;
                $rarity = Enchantment::RARITY_COMMON;
                break;
            case "Random Uncommon Enchantment (50 XP Levels)":
                $levels = 50;
                $rarity = Enchantment::RARITY_UNCOMMON;
                break;
            case "Random Rare Enchantment (80 XP Levels)":
                $levels = 80;
                $rarity = Enchantment::RARITY_RARE;
                break;
            case "Random Legendary Enchantment (110 XP Levels)":
                $levels = 110;
                $rarity = Enchantment::RARITY_MYTHIC;
                break;
            default:
                return;
        }
        if($player->getXpLevel() < $levels) {
            $player->sendMessage(Translation::getMessage("notEnoughLevels"));
            return;
        }
        $item = (new EnchantmentBook(ItemManager::getRandomEnchantment($rarity)))->getItemForm();
        $player->subtractXpLevels($levels);
        $player->sendMessage(Translation::getMessage("buy", [
            "amount" => TextFormat::GREEN . "1",
            "item" => TextFormat::DARK_GREEN . $item->getCustomName(),
            "price" => TextFormat::LIGHT_PURPLE . "$levels XP levels",
        ]));
        $player->getInventory()->addItem($item);
    }
}