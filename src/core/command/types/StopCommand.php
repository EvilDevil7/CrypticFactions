<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\lang\TranslationContainer;
use pocketmine\Server;

class StopCommand extends Command {

    /**
     * TestCommand constructor.
     */
    public function __construct() {
        parent::__construct("stop", "Stop the server.");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()){
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        /** @var CrypticPlayer $player */
        foreach(Server::getInstance()->getOnlinePlayers() as $player){
            if($player->getSession() !== null) $player->getSession()->save();
        }
        Command::broadcastCommandMessage($sender, new TranslationContainer("commands.stop.start"));
        $sender->getServer()->shutdown();
    }
}