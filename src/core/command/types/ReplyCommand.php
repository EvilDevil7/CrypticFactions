<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ReplyCommand extends Command {

    /**
     * ReplyCommand constructor.
     */
    public function __construct() {
        parent::__construct("reply", "Reply to a player.", "/reply <message>", ["r"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(count($args) < 1) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $lastTalked = $sender->getLastTalked();
        if($lastTalked === null || !$lastTalked instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        $message = implode(" ", $args);
        $sender->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "TO {$lastTalked->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
        $lastTalked->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "FROM {$sender->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
    }
}