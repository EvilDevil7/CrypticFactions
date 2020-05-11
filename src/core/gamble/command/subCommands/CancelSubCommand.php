<?php

declare(strict_types = 1);

namespace core\gamble\command\subCommands;

use core\command\utils\SubCommand;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class CancelSubCommand extends SubCommand {

    /**
     * CancelSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("cancel", "/coinflip cancel");
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
        if($this->getCore()->getGambleManager()->getCoinFlip($sender) === null) {
            $sender->sendMessage(Translation::getMessage("invalidCoinFlip"));
            return;
        }
        $this->getCore()->getGambleManager()->removeCoinFlip($sender);
    }
}