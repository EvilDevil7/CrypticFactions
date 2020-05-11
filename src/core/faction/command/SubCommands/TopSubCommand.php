<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TopSubCommand extends SubCommand {

    /**
     * TopSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("top", "/faction top <money/power>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        switch($args[1]) {
            case "money":
                $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT name, balance FROM factions ORDER BY balance DESC LIMIT 10");
                $stmt->execute();
                $stmt->bind_result($name, $balance);
                $place = 1;
                $text = $text = TextFormat::DARK_AQUA . TextFormat::BOLD . "TOP 10 RICHEST FACTIONS";
                while($stmt->fetch()) {
                    $text .= "\n" . TextFormat::BOLD . TextFormat::AQUA . "$place. " . TextFormat::RESET . TextFormat::DARK_GREEN . $name . TextFormat::DARK_GRAY . " | " . TextFormat::GREEN . "$" . $balance;
                    $place++;
                }
                $stmt->close();
                $sender->sendMessage($text);
                break;
            case "power":
                $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT name, strength FROM factions ORDER BY strength DESC LIMIT 10");
                $stmt->execute();
                $stmt->bind_result($name, $strength);
                $place = 1;
                $text = $text = TextFormat::DARK_PURPLE . TextFormat::BOLD . "TOP 10 STRONGEST FACTIONS";
                while($stmt->fetch()) {
                    $text .= "\n" . TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "$place. " . TextFormat::RESET . TextFormat::DARK_AQUA . $name . TextFormat::DARK_GRAY . " | " . TextFormat::AQUA  . $strength . " STR";
                    $place++;
                }
                $stmt->close();
                $sender->sendMessage($text);
                break;
            default:
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                return;
                break;
        }
    }
}