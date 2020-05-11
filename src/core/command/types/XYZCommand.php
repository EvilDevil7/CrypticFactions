<?php

declare(strict_types = 1);

namespace core\command\types;

use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\utils\TextFormat;

class XYZCommand extends Command {

    /**
     * XYZCommand constructor.
     */
    public function __construct() {
        parent::__construct("xyz", "Show your coordinates.", "/xyz <on/off>", ["coords"]);
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
        if(!isset($args[0])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        if($sender->isInStaffMode()){
            $sender->sendMessage(TextFormat::RED . "You cant do this on staff mode.");
            return;
        }
        switch($args[0]) {
            case "on":
                $pk = new GameRulesChangedPacket();
                $pk->gameRules = [
                    "showcoordinates" => [
                        1,
                        true
                    ]
                ];
                $sender->sendDataPacket($pk);
                $sender->sendMessage(Translation::getMessage("coordsShowChange", [
                    "mode" => $args[0]
                ]));
                return;
            case "off":
                $pk = new GameRulesChangedPacket();
                $pk->gameRules = [
                    "showcoordinates" => [
                        1,
                        false
                    ]
                ];
                $sender->sendDataPacket($pk);
                $sender->sendMessage(Translation::getMessage("coordsShowChange", [
                    "mode" => $args[0]
                ]));
                return;
            default:
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                return;
        }
    }
}
