<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Faction;
use core\faction\FactionException;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FixSubCommand extends SubCommand {

    /**
     * DisbandSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("fix", "/faction fix <user>");
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
        if(!$sender->isOp()) {
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $target = $sender->getServer()->getPlayer($args[1]);
        if(!$target instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        $target->setFactionRole(null);
        $target->setFaction(null);
        $sender->sendMessage("Set " . $target->getName() . "'s Faction to none");
    }
}