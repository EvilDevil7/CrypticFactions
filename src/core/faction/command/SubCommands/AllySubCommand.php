<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Faction;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AllySubCommand extends SubCommand {

    /**
     * AllySubCommand constructor.
     */
    public function __construct() {
        parent::__construct("ally", "/faction ally <faction>");
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
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
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
        $faction = $this->getCore()->getFactionManager()->getFaction($args[1]);
        if($faction === null) {
            $sender->sendMessage(Translation::getMessage("invalidFaction"));
            return;
        }
        if(count($faction->getAllies()) >= Faction::MAX_ALLIES) {
            $sender->sendMessage(Translation::getMessage("factionMaxAllies", [
                "faction" => TextFormat::RED . $faction->getName()
            ]));
            return;
        }
        if($faction->isAllying($sender->getFaction())) {
            $sender->getFaction()->addAlly($faction);
            $faction->addStrength(Faction::POWER_PER_ALLY);
            $sender->getFaction()->addStrength(Faction::POWER_PER_ALLY);
            $faction->addAlly($sender->getFaction());
            foreach($faction->getOnlineMembers() as $member) {
                $member->sendMessage(Translation::getMessage("allyAdd", [
                    "faction" => TextFormat::LIGHT_PURPLE . $sender->getFaction()->getName()
                ]));
            }
            foreach($sender->getFaction()->getOnlineMembers() as $member) {
                $member->sendMessage(Translation::getMessage("allyAdd", [
                    "faction" => TextFormat::LIGHT_PURPLE . $faction->getName()
                ]));
            }
        }
        else {
            $sender->getFaction()->addAllyRequest($faction);
            foreach($faction->getOnlineMembers() as $member) {
                $member->sendMessage(Translation::getMessage("allyRequest", [
                    "senderFaction" => TextFormat::GREEN . $sender->getFaction()->getName(),
                    "faction" => TextFormat::LIGHT_PURPLE . $faction->getName()
                ]));
            }
            foreach($sender->getFaction()->getOnlineMembers() as $member) {
                $member->sendMessage(Translation::getMessage("allyAdd", [
                    "senderFaction" => TextFormat::GREEN . $sender->getFaction()->getName(),
                    "faction" => TextFormat::LIGHT_PURPLE . $faction->getName()
                ]));
            }
        }
    }
}