<?php

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class FeedCommand extends Command {

    /**
     * FeedCommand constructor.
     */
    public function __construct() {
        parent::__construct("feed", "Restore hunger");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if((!$sender instanceof CrypticPlayer) or ((!$sender->isOp()) and (!$sender->hasPermission("permission.tier1")))) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $sender->setFood(20);
        $sender->sendMessage(Translation::getMessage("hungerRestored"));
    }
}