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

class PromoteSubCommand extends SubCommand {

    /**
     * PromoteSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("promote", "/faction promote <player>");
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
        $player = $this->getCore()->getServer()->getPlayer($args[1]);
        if(!$player instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        if(!$sender->getFaction()->isInFaction($player)) {
            $sender->sendMessage(Translation::getMessage("notFactionMember", [
                "name" => TextFormat::RED . $player->getName()
            ]));
            return;
        }
        if($player->getFactionRole() === Faction::OFFICER) {
            $sender->sendMessage(Translation::getMessage("cannotPromote", [
                "name" => TextFormat::RED . $player->getName()
            ]));
            return;
        }
        foreach($sender->getFaction()->getOnlineMembers() as $member) {
            $member->sendMessage(Translation::getMessage("promoted", [
                "name" => TextFormat::GREEN . $player->getName(),
                "sender" => TextFormat::LIGHT_PURPLE . $sender->getName()
            ]));
        }
        $sender->getFaction()->promote($player);
    }
}