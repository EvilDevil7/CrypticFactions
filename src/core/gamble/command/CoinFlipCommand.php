<?php

declare(strict_types = 1);

namespace core\gamble\command;

use core\command\utils\Command;
use core\gamble\command\subCommands\AddSubCommand;
use core\gamble\command\subCommands\CancelSubCommand;
use core\gamble\command\subCommands\ListSubCommand;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class CoinFlipCommand extends Command {

    /**
     * CoinFlipCommand constructor.
     */
    public function __construct() {
        parent::__construct("coinflip", "Manage coin flipping", "/coinflip <list/add/cancel>", ["cf"]);
        $this->addSubCommand(new AddSubCommand());
        $this->addSubCommand(new CancelSubCommand());
        $this->addSubCommand(new ListSubCommand());
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(isset($args[0])) {
            $subCommand = $this->getSubCommand($args[0]);
            if($subCommand !== null) {
                $subCommand->execute($sender, $commandLabel, $args);
                return;
            }
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $sender->sendMessage(Translation::getMessage("usageMessage", [
            "usage" => $this->getUsage()
        ]));
        return;
    }
}
