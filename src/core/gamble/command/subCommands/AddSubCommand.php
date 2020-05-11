<?php

declare(strict_types = 1);

namespace core\gamble\command\subCommands;

use core\command\utils\SubCommand;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddSubCommand extends SubCommand {

    /**
     * AddSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("add", "/coinflip add <amount>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        if($this->getCore()->getGambleManager()->getCoinFlip($sender) !== null) {
            $sender->sendMessage(Translation::getMessage("existingCoinFlip"));
            return;
        }
        $amount = (int)$args[1];
        if(!is_numeric($amount)) {
            $sender->sendMessage(Translation::getMessage("invalidAmount"));
            return;
        }
        if($amount < 10000) {
            $sender->sendMessage(Translation::getMessage("notEnoughMoneyRankUp", [
                "amount" => TextFormat::DARK_RED . "$10000"
            ]));
            return;
        }
        $sender->getCore()->getGambleManager()->addCoinFlip($sender, $amount);
        $sender->sendMessage(Translation::getMessage("addCoinFlip"));
    }
}
