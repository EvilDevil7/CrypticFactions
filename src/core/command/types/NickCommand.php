<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\rank\Rank;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NickCommand extends Command {

    /**
     * NickCommand constructor.
     */
    public function __construct() {
        parent::__construct("nick", "Change your name to something.");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if((!$sender instanceof CrypticPlayer) or ($sender->getRank()->getIdentifier() < 8 and !in_array($sender->getRank()->getIdentifier(), [Rank::OVERLORD, Rank::YOUTUBER]))){
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $name = implode(" ", $args);
        if($name == "reset" or $name == "off"){
            $sender->setDisplayName($sender->getName());
            $sender->sendMessage(TextFormat::LIGHT_PURPLE . "You have reset your nick to your name.");
            return;
        }
        $sender->setDisplayName($name);
        $sender->sendMessage(TextFormat::AQUA . "You name has been set to " . TextFormat::YELLOW . $name);
        return;
    }
}