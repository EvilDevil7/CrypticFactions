<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class TestCommand extends Command {

    /**
     * TestCommand constructor.
     */
    public function __construct() {
        parent::__construct("test", "Has a mysterious function, only could be executed by DavidGamingzz.", "/test");
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
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if($sender->getName() !== "DavidGamingzz") {
            $sender->sendMessage(Translation::RED . TextFormat::RED . "You just got caught " . TextFormat::DARK_RED . "LACKING" . TextFormat::RED . ". Only someone under the username of " . TextFormat::YELLOW . "DavidGamingzz" . TextFormat::RED . " can use this command.");
            return;
        }
        $sender->setMotion($sender->getMotion()->add(0, 1, 0));
    }
}