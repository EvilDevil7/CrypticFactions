<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class AddPowerSubCommand extends SubCommand {

    /**
     * AddPowerSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("addpower", "/faction addpower <faction> <amount>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(($sender->isOp() and $sender instanceof CrypticPlayer) or $sender instanceof ConsoleCommandSender) {
            if(isset($args[2])) {
                $faction = $this->getCore()->getFactionManager()->getFaction($args[1]);
                if($faction === null) {
                    $sender->sendMessage("invalidFaction");
                    return;
                }
                $amount = (int)$args[2];
                if(!is_numeric($amount)) {
                    $sender->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $faction->addStrength($amount);
                $faction = $faction->getName();
                $sender->sendMessage(Translation::getMessage("factionAddPowerSuccess", [
                    "amount" => TextFormat::LIGHT_PURPLE . $amount,
                    "name" => $faction
                ]));
                return;
            }
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
    }
}
