<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\item\types\BossEgg;
use core\item\types\HolyBox;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class GiveItemCommand extends Command {

    /**
     * GiveItemCommand constructor.
     */
    public function __construct() {
        parent::__construct("giveitem", "Give item to a player.", "/giveitem <player> <item>");
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
            if(!isset($args[1])) {
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                return;
            }
            $player = $this->getCore()->getServer()->getPlayer($args[0]);
            if(!$player instanceof CrypticPlayer) {
                $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
            switch($args[1]) {
                case "holybox":
                    if(!isset($args[2])) {
                        $kits = $this->getCore()->getKitManager()->getSacredKits();
                        $kit = $kits[array_rand($kits)];
                    }
                    else {
                        $kit = $this->getCore()->getKitManager()->getKitByName($args[2]);
                    }
                    if($kit === null) {
                        $sender->sendMessage(Translation::getMessage("invalidKit"));
                    }
                    $player->getInventory()->addItem((new HolyBox($kit))->getItemForm());
                    break;
                case "boss":
                    if(!isset($args[2])) {
                        $sender->sendMessage(Translation::getMessage("usageMessage", [
                            "usage" => "/giveitem <player> boss <type>"
                        ]));
                        return;
                    }
                    $boss = $this->getCore()->getCombatManager()->getBossNameByIdentifier((int)$args[2]);
                    if($boss === null) {
                        $sender->sendMessage(Translation::getMessage("invalidBoss"));
                    }
                    $boss = $this->getCore()->getCombatManager()->getIdentifierByName($boss);
                    $player->getInventory()->addItem((new BossEgg($boss))->getItemForm());
                    break;
                default:
                    $sender->sendMessage(Translation::getMessage("usageMessage", [
                        "usage" => $this->getUsage()
                    ]));
                    break;
            }
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}