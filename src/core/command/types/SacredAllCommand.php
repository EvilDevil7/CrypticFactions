<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\item\types\SacredStone;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class SacredAllCommand extends Command {

    /**
     * SacredAllCommand constructor.
     */
    public function __construct() {
        parent::__construct("sacredall", "Give sacred stones to all players.", "/sacredall <amount>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof ConsoleCommandSender or $sender->isOp()) {
            if(!isset($args[0])) {
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                return;
            }
            if(!$sender instanceof ConsoleCommandSender) {
                if(!$sender->isOp()) {
                    $sender->sendMessage(Translation::RED . TextFormat::RED . "You just got caught " . TextFormat::DARK_RED . "LACKING" . TextFormat::RED . ". Only someone under the username of " . TextFormat::YELLOW . "JTJamez" . TextFormat::RED . " can use this command.");
                    return;
                }
            }
            $amount = is_numeric($args[0]) ? (int)$args[0] : 1;
            $item = (new SacredStone())->getItemForm()->setCount($amount);
            $this->getCore()->getServer()->broadcastMessage(Translation::getMessage("sacredStoneAll", [
                "name" => TextFormat::AQUA . $sender->getName(),
                "amount" => TextFormat::YELLOW . $amount,
            ]));
            foreach($this->getCore()->getServer()->getOnlinePlayers() as $player) {
                if($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
            }
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}