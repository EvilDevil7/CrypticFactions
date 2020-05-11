<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\task\TeleportTask;
use core\command\utils\SubCommand;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class HomeSubCommand extends SubCommand {

    /**
     * HomeSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("home", "/faction home");
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
        if($sender->getFaction() === null) {
            $sender->sendMessage(Translation::getMessage("beInFaction"));
            return;
        }
        if($sender->getFaction()->getHome() === null) {
            $sender->sendMessage(Translation::getMessage("homeNotSet"));
            return;
        }
        if($sender->isTeleporting()) {
            $sender->sendMessage(Translation::getMessage("alreadyTeleporting", [
                "name" => "You are"
            ]));
            return;
        }
        $this->getCore()->getScheduler()->scheduleRepeatingTask(new TeleportTask($sender, $sender->getFaction()->getHome(), 5), 20);
    }
}