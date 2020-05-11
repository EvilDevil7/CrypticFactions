<?php

declare(strict_types = 1);

namespace core\command\forms;

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

class QuestShopForm extends MenuForm {

    /**
     * QuestShopForm constructor.
     *
     * @param CrypticPlayer $player
     */
    public function __construct(CrypticPlayer $player) {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Quest Shop";
        $text = "Quest points: " . $player->getQuestPoints();
        $options = [];
        $options[] = new MenuOption("$10,000 (1 Point)");
        $options[] = new MenuOption("Random Enchantment (10 Points)");
        $options[] = new MenuOption("Sacred Stone (30 Points)");
        $options[] = new MenuOption("Iron Golem Spawner (80 Points)");
        $options[] = new MenuOption("Holy Box (150 Points)");
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
            case "$10,000 (1 Point)":
                $points = 1;
                $item = (new MoneyNote(10000))->getItemForm();
                break;
            case "Random Enchantment (10 Points)":
                $points = 10;
                $item = (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm();
                break;
            case "Sacred Stone (30 Points)":
                $points = 30;
                $item = (new SacredStone())->getItemForm();
                break;
            case "Iron Golem Spawner (80 Points)":
                $points = 80;
                $item = Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                    new IntTag("EntityId", Entity::IRON_GOLEM)
                ]));
                $item->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Iron Golem Spawner");
                break;
            case "Holy Box (150 Points)":
                $kits = Cryptic::getInstance()->getKitManager()->getSacredKits();
                $kit = $kits[array_rand($kits)];
                $points = 150;
                $item = (new HolyBox($kit))->getItemForm();
                break;
            default:
                return;
        }
        if($player->getQuestPoints() < $points) {
            $player->sendMessage(Translation::getMessage("notEnoughPoints"));
            return;
        }
        $player->subtractQuestPoints($points);
        $player->sendMessage(Translation::getMessage("buy", [
            "amount" => TextFormat::GREEN . "1",
            "item" => TextFormat::DARK_GREEN . $item->getCustomName(),
            "price" => TextFormat::LIGHT_PURPLE . "$points quest points",
        ]));
        $player->getInventory()->addItem($item);
    }
}