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

class JoinSubCommand extends SubCommand {

    /**
     * JoinSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("join", "/faction join <faction>");
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
        $faction = $this->getCore()->getFactionManager()->getFaction($args[1]);
        if($faction === null) {
            $sender->sendMessage(Translation::getMessage("invalidFaction"));
            return;
        }
        if(!$faction->isInvited($sender)) {
            $sender->sendMessage(Translation::getMessage("notInvited", [
                "faction" => TextFormat::RED . $faction->getName()
            ]));
            return;
        }
        if(count($faction->getMembers()) >= Faction::MAX_MEMBERS) {
            $sender->sendMessage(Translation::getMessage("factionMaxMembers", [
                "faction" => TextFormat::RED . $faction->getName()
            ]));
            return;
        }
        if($sender->getFaction() !== null) {
            $sender->sendMessage(Translation::getMessage("mustLeaveFaction"));
            return;
        }
        $faction->addMember($sender);
        foreach($faction->getOnlineMembers() as $member) {
            $member->sendMessage(Translation::getMessage("factionJoin", [
                "name" => TextFormat::GREEN . $sender->getName()
            ]));
            $faction->addStrength(Faction::POWER_PER_JOIN);
        }
    }
}