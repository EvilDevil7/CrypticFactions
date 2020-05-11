<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class RemoveHomeCommand extends Command {

    /**
     * RemoveHomeCommand constructor.
     */
    public function __construct() {
        parent::__construct("removehome", "Delete a home", "/removehome <name>", ["rmhome", "delhome"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof CrypticPlayer) {
            if(isset($args[0])) {
                $home = $sender->getHome($args[0]);
                if($home === null) {
                    $sender->sendMessage(Translation::getMessage("invalidHome"));
                    return;
                }
                $sender->deleteHome($args[0]);
                $sender->sendMessage(Translation::getMessage("deleteHome"));
                return;
            }
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}