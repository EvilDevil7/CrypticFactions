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

class OverClaimSubCommand extends SubCommand {

    /**
     * OverClaimSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("overclaim", "/faction overclaim");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     * @throws FactionException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if($sender->getLevel()->getName() !== Faction::CLAIM_WORLD) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $faction = $sender->getFaction();
        if($faction === null) {
            $sender->sendMessage(Translation::getMessage("beInFaction"));
            return;
        }
        if($sender->getFactionRole() !== Faction::LEADER) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $factionManager = $this->getCore()->getFactionManager();
        if(($claim = $factionManager->getClaimInPosition($sender)) === null) {
            $sender->sendMessage(Translation::getMessage("notClaimed"));
            return;
        }
        if(count($faction->getMembers()) < Faction::MEMBERS_NEEDED_TO_CLAIM) {
            $sender->sendMessage(Translation::getMessage("notEnoughFactionMembersToClaim"));
            return;
        }
        if($claim->getFaction()->getStrength() >= $faction->getStrength()) {
            $sender->sendMessage(Translation::getMessage("notEnoughStrength"));
            return;
        }
        $factionManager->overClaim($faction, $claim);
        $sender->sendMessage(Translation::getMessage("overclaimSuccess"));
    }
}