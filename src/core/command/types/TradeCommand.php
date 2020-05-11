<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\trade\TradeSession;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TradeCommand extends Command {

    /**
     * TradeCommand constructor.
     */
    public function __construct() {
        parent::__construct("trade", "Ask to trade with someone.", "/trade <ask/accept/deny> <player>");
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
        $player = $this->getCore()->getServer()->getPlayer($args[1]);
        if(!$player instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        switch($args[0]) {
            case "ask":
                if($sender->isRequestingTrade($player)) {
                    $sender->sendMessage(Translation::getMessage("alreadyRequest"));
                    return;
                }
                $sender->addTradeRequest($player);
                $sender->sendMessage(Translation::getMessage("requestTrade", [
                    "name" => "You have",
                    "player" => TextFormat::YELLOW . $player->getName()
                ]));
                $player->sendMessage(Translation::getMessage("requestTrade", [
                    "name" => TextFormat::YELLOW . $sender->getName() . TextFormat::GRAY . " has",
                    "player" => "you"
                ]));
                break;
            case "accept":
                if(!$player->isRequestingTrade($sender)) {
                    $sender->sendMessage(Translation::getMessage("didNotRequest"));
                    return;
                }
                foreach($this->getCore()->getTradeManager()->getSessions() as $session) {
                    if($session->getSender()->getRawUniqueId() === $player->getRawUniqueId()) {
                        $sender->sendMessage(Translation::getMessage("alreadyTrading", [
                            "name" => "{$player->getName()}"
                        ]));
                        return;
                    }
                    if($session->getReceiver()->getRawUniqueId() === $player->getRawUniqueId()) {
                        $sender->sendMessage(Translation::getMessage("alreadyTrading", [
                            "name" => "{$player->getName()}"
                        ]));
                        return;
                    }
                }
                $player->removeTradeRequest($sender);
                $player->sendMessage(Translation::getMessage("acceptRequest"));
                $session = new TradeSession($player, $sender);
                $this->getCore()->getTradeManager()->addSession($session);
                $session->sendMenus();
                break;
            case "deny":
                if(!$player->isRequestingTrade($sender)) {
                    $sender->sendMessage(Translation::getMessage("didNotRequest"));
                    return;
                }
                $player->removeTradeRequest($sender);
                $player->sendMessage(Translation::getMessage("denyRequest"));
                break;
            default:
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                break;
        }
    }
}