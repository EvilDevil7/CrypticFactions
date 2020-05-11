<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class PVPHUDCommand extends Command {

    /**
     * VanishCommand constructor.
     */
    public function __construct() {
        parent::__construct("pvphud", "Toggle pvp hud", "/pvphud");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @throws TranslationException
     * @throws \libs\utils\UtilsException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $sender->togglePVPHUD();
    }
}