<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\task\CheckVoteTask;
use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class VoteCommand extends Command {

    /**
     * VoteCommand constructor.
     */
    public function __construct() {
        parent::__construct("vote", "Check if you've voted yet.", "/vote");
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
        if($sender->hasVoted()) {
            $sender->sendMessage(Translation::getMessage("alreadyVoted"));
            return;
        }
        if($sender->isCheckingForVote()) {
            $sender->sendMessage(Translation::getMessage("checkingVote"));
            return;
        }
        $this->getCore()->getServer()->getAsyncPool()->submitTaskToWorker(new CheckVoteTask($sender->getName()), 1);
        return;
    }
}