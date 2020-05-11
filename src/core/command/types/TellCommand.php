<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TellCommand extends Command {

    /**
     * TellCommand constructor.
     */
    public function __construct() {
        parent::__construct("tell", "Send a message to a player.", "/tell <player> <message>", ["whisper, w, message, msg"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(count($args) < 2) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $name = array_shift($args);
        $player = $this->getCore()->getServer()->getPlayer($name);
        if($sender === $player or $player === null) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        $message = implode(" ", $args);
        $sender->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "TO {$player->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
        $player->sendMessage(TextFormat::DARK_GREEN . TextFormat::BOLD . "FROM {$sender->getName()}: " . TextFormat::RESET . TextFormat::GREEN . $message);
        if($player instanceof CrypticPlayer) {
            $player->setLastTalked($sender);
        }
        if($sender instanceof CrypticPlayer) {
            $sender->setLastTalked($player);
        }
    }
}