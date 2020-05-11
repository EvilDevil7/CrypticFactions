<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

class AddPermissionCommand extends Command {

    /**
     * AddPermissionCommand constructor.
     */
    public function __construct() {
        parent::__construct("addpermission", "Add a permission to a player.", "/addpermission <player> <permission>");
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
            if(isset($args[1])) {
                $player = $sender->getServer()->getPlayer($args[0]);
                if($player instanceof CrypticPlayer) {
                    $player->addPermanentPermission((string)$args[1]);
                    $sender->sendMessage(Translation::getMessage("addPermission", [
                        "permission" => $args[1],
                        "name" => $player->getName()
                    ]));
                    return;
                }
                $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}