<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\task\TeleportTask;
use core\command\utils\Command;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class BossCommand extends Command {

    /**
     * BossCommand constructor.
     */
    public function __construct() {
        parent::__construct("boss", "Teleport to the boss arena.");
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
            $level = $sender->getServer()->getLevelByName("bossarena");
            if($level === null) {
                return;
            }
            $spawn = $level->getSpawnLocation();
            Cryptic::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($sender, $spawn, 5), 20);
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }
}