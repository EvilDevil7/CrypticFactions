<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Faction;
use core\faction\FactionException;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DisbandSubCommand extends SubCommand {

    /**
     * DisbandSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("disband", "/faction disband");
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
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if($sender->getFaction() === null) {
            $sender->sendMessage(Translation::getMessage("beInFaction"));
            return;
        }
        if($sender->getFactionRole() !== Faction::LEADER) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        foreach($sender->getFaction()->getOnlineMembers() as $player) {
            $player->addTitle(TextFormat::GREEN . TextFormat::BOLD . "Announcement", TextFormat::GRAY . $sender->getFaction()->getName() . " has been disbanded", 20, 60, 20);
        }
        $this->getCore()->getFactionManager()->removeFaction($sender->getFaction()->getName());
    }
}