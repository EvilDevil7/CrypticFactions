<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\provider\Session;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveMoneyCommand extends Command {

    /**
     * RemoveMoneyCommand constructor.
     */
    public function __construct() {
        parent::__construct("removemoney", "Remove money from a player's balance.", "/removemoney <player> <amount>", ["takemoney", "rmmoney"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void{
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
		$player = $this->getCore()->getServer()->getPlayer($args[0]);
		if(!$player instanceof CrypticPlayer) {
			$stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT balance FROM players WHERE username = ?");
			$stmt->bind_param("s", $args[0]);
			$stmt->execute();
			$stmt->bind_result($balance);
			$stmt->fetch();
			$stmt->close();
			if($balance === null) {
				$sender->sendMessage(Translation::getMessage("invalidPlayer"));
				return;
			}
		}
		if(!is_numeric($args[1])) {
			$sender->sendMessage(Translation::getMessage("notNumeric"));
			return;
		}
		if(isset($balance)) {
			$stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET balance = balance - ? WHERE username = ?");
			$stmt->bind_param("is", $args[1], $args[0]);
			$stmt->execute();
			$stmt->close();
		}
		else {
			$player->addToBalance((int)$args[1]);
		}
		$sender->sendMessage(Translation::getMessage("addMoneySuccess", [
			"amount" => TextFormat::GREEN . "$" . $args[1],
			"name" => TextFormat::GOLD . $player instanceof CrypticPlayer ? $player->getName() : $args[0]
		]));
    }
}