<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\FactionManager;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class MapSubCommand extends SubCommand {

    /**
     * MapSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("map", "/faction map");
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
        FactionManager::sendFactionMap($sender);
    }
}