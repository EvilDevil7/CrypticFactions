<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StaffChatCommand extends Command {

    /**
     * StaffChatCommand constructor.
     */
    public function __construct() {
        parent::__construct("staffchat", "Toggle staff chat.", "/staffchat", ["sc"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if((!$sender instanceof CrypticPlayer) or (!$sender->hasPermission("permission.staff"))) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $mode = CrypticPlayer::PUBLIC;
        if($sender->getChatMode() !== CrypticPlayer::STAFF) {
            $mode = CrypticPlayer::STAFF;
        }
        $sender->setChatMode($mode);
        $sender->sendMessage(Translation::getMessage("chatModeSwitch", [
            "mode" =>  TextFormat::GREEN . strtoupper($sender->getChatModeToString())
        ]));
    }
}