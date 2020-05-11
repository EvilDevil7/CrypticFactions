<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Claim;
use core\faction\Faction;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class ClaimSubCommand extends SubCommand {

    /**
     * ClaimSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("claim", "/faction claim");
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
        if($factionManager->getClaimInPosition($sender) !== null) {
            $sender->sendMessage(Translation::getMessage("inClaim"));
            return;
        }
        if(count($faction->getMembers()) < Faction::MEMBERS_NEEDED_TO_CLAIM) {
            $sender->sendMessage(Translation::getMessage("notEnoughFactionMembersToClaim"));
            return;
        }
        $factionManager->addClaim(new Claim($sender->getX() >> 4, $sender->getZ() >> 4, $faction));
        $sender->sendMessage(Translation::getMessage("claimSuccess"));
    }
}