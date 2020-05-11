<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\task\TeleportTask;
use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class PVPCommand extends Command {

    /**
     * PVPCommand constructor.
     */
    public function __construct() {
        parent::__construct("pvp", "Teleport to pvp arena.");
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
            $level = $sender->getServer()->getLevelByName("pvp");
            if($level === null) {
                return;
            }
            $spawn = $level->getSpawnLocation();
            $this->getCore()->getScheduler()->scheduleRepeatingTask(new TeleportTask($sender, $spawn, 5), 20);
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}