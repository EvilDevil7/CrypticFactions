<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AddTagCommand extends Command {

    /**
     * AddTagCommand constructor.
     */
    public function __construct() {
        parent::__construct("addtag", "Give a player tag.");
        $this->setAliases(["givetag"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(count($args) < 2){
            $sender->sendMessage("§l§8(§6!§8)§r §7Usage: /addtag <player> <tag>§r");
            return;
        }
        if(!isset($args[1])){
            $sender->sendMessage("§l§8(§6!§8)§r §7Usage: /addtag <player> <tag>§r");
            return;
        }
        if(($player = Server::getInstance()->getPlayer($args[0])) === null){
            $sender->sendMessage("§l§8(§c!§8)§r §7That player cannot be found.§r");
            return;
        }
        /** @var CrypticPlayer $player */
        if(Cryptic::getInstance()->getTagManager()->giveTag($player, $args[1]))
        $sender->sendMessage("§l§8(§a!§8)§r §7You've successfully given the tag, §a" . $args[1] . "§7.§r");
    }
}