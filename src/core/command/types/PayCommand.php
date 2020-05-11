<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class PayCommand extends Command {

    /**
     * PayCommand constructor.
     */
    public function __construct() {
        parent::__construct("pay", "Pay a player.", "/pay <player> <amount>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, $commandLabel, array $args): void {
        if($sender instanceof CrypticPlayer) {
            if(isset($args[1])) {
                $player = $sender->getServer()->getPlayer($args[0]);
                if(!$player instanceof CrypticPlayer) {
                    $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                    return;
                }
                if($player->getName() === $sender->getName()) {
                    $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                    return;
                }
                $amount = (int)$args[1];
                if((!is_numeric($args[1])) or $amount <= 0) {
                    $sender->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                if($sender->getBalance() < $amount) {
                    $sender->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $sender->subtractFromBalance($amount);
                $player->addToBalance($amount);
                $sender->sendMessage(Translation::getMessage("payMoneyTo", [
                    "amount" => TextFormat::LIGHT_PURPLE . "$$amount",
                    "name" => TextFormat::GREEN . $player->getName()
                ]));
                $player->sendMessage(Translation::getMessage("receiveMoneyFrom", [
                    "amount" => TextFormat::LIGHT_PURPLE . "$$amount",
                    "name" => TextFormat::GREEN . $sender->getName()
                ]));
                return;
            }
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $sender->sendMessage("noPermission");
    }
}