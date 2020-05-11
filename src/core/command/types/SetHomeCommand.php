<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class SetHomeCommand extends Command {

    /**
     * SetHomeCommand constructor.
     */
    public function __construct() {
        parent::__construct("sethome", "Set a home", "/sethome <name>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof CrypticPlayer) {
            if(count($sender->getHomes()) >= $sender->getRank()->getHomeLimit()) {
                $sender->sendMessage(Translation::getMessage("maxReached"));
            }
            if($sender->getGamemode() === CrypticPlayer::SPECTATOR) {
                $sender->sendMessage(Translation::getMessage("noPermission"));
                return;
            }
            if($sender->hasVanished()) {
                $sender->sendMessage(Translation::getMessage("noPermission"));
                return;
            }
            if(isset($args[0])) {
                $home = $sender->getHome($args[0]);
                if($home !== null) {
                    $sender->sendMessage(Translation::getMessage("homeExist"));
                    return;
                }
                $sender->sendMessage(Translation::getMessage("setHome"));
                $sender->addHome($args[0], $sender->getPosition());
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