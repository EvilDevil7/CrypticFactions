<?php

declare(strict_types = 1);

namespace core\gamble\command\subCommands;

use core\command\utils\SubCommand;
use core\gamble\command\forms\CoinFlipListForm;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class ListSubCommand extends SubCommand {

    /**
     * ListSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("list", "/coinflip list");
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
        $sender->sendForm(new CoinFlipListForm($sender));
    }
}