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

class DemoteSubCommand extends SubCommand {

    /**
     * DemoteSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("demote", "/faction demote <player>");
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
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $player = $this->getCore()->getServer()->getPlayer($args[1]);
        if(!$player instanceof CrypticPlayer) {
            $name = $args[1];
            $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT faction, factionRole FROM players WHERE username = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->bind_result($faction, $factionRole);
            $stmt->fetch();
            $stmt->close();
            if($faction === null and $factionRole === null) {
                $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
        }
        else {
            $faction = $player->getFaction()->getName();
            $factionRole = $player->getFactionRole();
            $name = $player->getName();
        }
        if($faction !== $sender->getFaction()->getName()) {
            $sender->sendMessage(Translation::getMessage("notFactionMember", [
                "name" => TextFormat::RED . $name
            ]));
            return;
        }
        if($factionRole >= $sender->getFactionRole() or $factionRole === Faction::RECRUIT) {
            $sender->sendMessage(Translation::getMessage("cannotDemote", [
                "name" => TextFormat::RED . $name
            ]));
            return;
        }
        if(!$player instanceof CrypticPlayer) {
            $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET factionRole = factionRole - 1 WHERE username = ?");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
        }
        else {
            $player->setFactionRole($player->getFactionRole() - 1);
        }
        foreach($sender->getFaction()->getOnlineMembers() as $member) {
            $member->sendMessage(Translation::getMessage("demoted", [
                "name" => TextFormat::GREEN . $name,
                "sender" => TextFormat::LIGHT_PURPLE . $sender->getName()
            ]));
        }
    }
}