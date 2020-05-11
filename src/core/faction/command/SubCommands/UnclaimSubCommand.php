<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Faction;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class UnclaimSubCommand extends SubCommand {

    /**
     * UnclaimSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("unclaim", "/faction unclaim ");
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
        if($sender->getFactionRole() !== Faction::LEADER) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $factionManager = $this->getCore()->getFactionManager();
        if(($claim = $factionManager->getClaimInPosition($sender->asPosition())) === null) {
            $sender->sendMessage(Translation::getMessage("notClaimed"));
            return;
        }
        if(!$claim->getFaction()->isInFaction($sender)) {
            $sender->sendMessage(Translation::getMessage("doNotOwnClaim"));
            return;
        }
        $factionManager->removeClaim($claim);
        $sender->sendMessage(Translation::getMessage("unclaimSuccess"));
    }
}