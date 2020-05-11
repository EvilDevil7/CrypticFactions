<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\forms\RenameItemForm;
use core\command\utils\Command;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\item\Durable;
use pocketmine\Player;

class RenameCommand extends Command {

    /**
     * RenameCommand constructor.
     */
    public function __construct() {
        parent::__construct("rename", "Rename an item", "/rename");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof Player) {
            $item = $sender->getInventory()->getItemInHand();
            if(!$item instanceof Durable) {
                $sender->sendMessage(Translation::getMessage("invalidItem"));
                return;
            }
            $sender->sendForm(new RenameItemForm());
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}