<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\FactionException;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class ForceDeleteSubCommand extends SubCommand {

    /**
     * ForceDeleteSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("forcedelete", "/faction forcedelete <faction>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws FactionException
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if((!$sender->isOp()) or (!$sender instanceof ConsoleCommandSender)) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $faction = $args[1];
        if($this->getCore()->getFactionManager()->getFaction($faction) === null) {
            $sender->sendMessage(Translation::getMessage("invalidFaction"));
            return;
        }
        $this->getCore()->getFactionManager()->removeFaction($faction);
        $sender->sendMessage(Translation::getMessage("forceDelete"));
    }
}