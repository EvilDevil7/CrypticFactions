<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\item\types\ChestKit;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\MenuForm;
use libs\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class KitListForm extends MenuForm {

    /**
     * KitListForm constructor.
     *
     * @param array $kits
     */
    public function __construct(array $kits) {
        $title = "§l§bKits§r";
        $text = "Select a kit.";
        $options = [];
        foreach($kits as $kit) {
            $options[] = new MenuOption($kit->getName());
        }
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
        $time = time();
        $kitManager = Cryptic::getInstance()->getKitManager();
        $name = explode("\n", $this->getOption($selectedOption)->getText())[0];
        $lowercaseName = strtolower($name);
        if(!$player->hasPermission("permission.$lowercaseName")) {
            $player->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $kit = $kitManager->getKitByName($name);
        $cooldown = $kitManager->getCooldown($kit->getName(), $player->getName());
        $cooldown = $kit->getCooldown() - ($time - $cooldown);
        if($cooldown > 0){
            $days = floor($cooldown / 86400);
            $hours = $hours = floor(($cooldown / 3600) % 24);
            $minutes = floor(($cooldown / 60) % 60);
            $seconds = $cooldown % 60;
            $msg = "";
            if($days >= 1) $msg .= $days . "days, ";
            if($hours >= 1) $msg .= $hours . "hours, ";
            if($minutes >= 1) $msg .= $minutes . "mintues, ";
            if($seconds >= 1) $msg .= $seconds . "seconds";
            $player->sendMessage(Translation::getMessage("kitCooldown", [
                "time" => TextFormat::RED . $msg
            ]));
            return;
        }
        $item = (new ChestKit($kitManager->getKitByName($name)))->getItemForm();
        if(!$player->getInventory()->canAddItem($item)) {
            $player->sendMessage(Translation::getMessage("fullInventory"));
            return;
        }
        $player->getInventory()->addItem($item);
        $player->sendMessage("§l§8(§a!§8)§r §7Selected Kit§8: §a" . $name . "§r");
        $kitManager->addToCooldown($kit->getName(), $player->getName(), $time);
    }
}