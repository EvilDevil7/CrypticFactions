<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class AddXPCommand extends Command {

    /**
     * AddXPCommand constructor.
     */
    public function __construct() {
        parent::__construct("addxp", "Add XP to a player", "/addxp <player> <amount>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $player = $this->getCore()->getServer()->getPlayer($args[0]);
        if(!$player instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        $amount = max(1, is_numeric($args[1]) ? (int)$args[1] : 1);
        $player->addXp($amount);
        $sender->sendMessage(Translation::getMessage("successAbuse"));
        return;
    }
}