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

class UnallySubCommand extends SubCommand {

    /**
     * UnallySubCommand constructor.
     */
    public function __construct() {
        parent::__construct("unally", "/faction unally <faction>");
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
        foreach($sender->getFaction()->getOnlineMembers() as $member) {
            $member->sendMessage(Translation::getMessage("unally", [
                "faction" => TextFormat::GREEN . $faction->getName()
            ]));
        }
        foreach($faction->getOnlineMembers() as $member) {
            $member->sendMessage(Translation::getMessage("unally", [
                "faction" => TextFormat::GREEN . $sender->getFaction()->getName()
            ]));
        }
        $faction->subtractStrength(Faction::POWER_PER_ALLY);
        $sender->getFaction()->subtractStrength(Faction::POWER_PER_ALLY);
        $faction->removeAlly($sender->getFaction());
        $sender->getFaction()->removeAlly($faction);
    }
}