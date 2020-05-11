<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\FactionException;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends SubCommand {

    /**
     * CreateSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("create", "/faction create <name>");
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
        if($sender->getFaction() !== null) {
            $sender->sendMessage(Translation::getMessage("mustLeaveFaction"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        if(strlen($args[1]) > 30) {
            $sender->sendMessage(Translation::getMessage("factionNameTooLong"));
            return;
        }
        $faction = $this->getCore()->getFactionManager()->getFaction($args[1]);
        if($faction !== null) {
            $sender->sendMessage(Translation::getMessage("existingFaction", [
                "faction" => TextFormat::RED . $args[1]
            ]));
            return;
        }
        $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT members FROM factions WHERE name = ?");
        $stmt->bind_param("s", $args[1]);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if($result !== null) {
            $sender->sendMessage(Translation::getMessage("existingFaction", [
                "faction" => TextFormat::RED . $args[1]
            ]));
            return;
        }
        $this->getCore()->getFactionManager()->createFaction($args[1], $sender);
        $sender->sendMessage(Translation::getMessage("factionCreate"));
    }
}