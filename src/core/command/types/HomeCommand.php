<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\task\TeleportTask;
use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class HomeCommand extends Command {

    /**
     * HomeCommand constructor.
     */
    public function __construct() {
        parent::__construct("home", "Teleport to a home");
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
            if(isset($args[0])) {
                $home = $sender->getHome($args[0]);
                if($home === null) {
                    $sender->sendMessage(TextFormat::DARK_RED . TextFormat::BOLD . "HOMES:");
                    $sender->sendMessage(TextFormat::WHITE . implode(", ", array_keys($sender->getHomes())));
                    return;
                }
                $this->getCore()->getScheduler()->scheduleRepeatingTask(new TeleportTask($sender, $home, 5), 20);
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