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

class LeaveSubCommand extends SubCommand {

    /**
     * LeaveSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("leave", "/faction leave");
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
        if($sender->getFactionRole() === Faction::LEADER) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        foreach($sender->getFaction()->getOnlineMembers() as $player) {
            $player->sendMessage(Translation::getMessage("factionLeave", [
                "name" => TextFormat::GREEN . $sender->getName()
            ]));
        }
        $sender->getFaction()->subtractStrength(Faction::POWER_PER_JOIN);
        $sender->getFaction()->removeMember($sender);
    }
}