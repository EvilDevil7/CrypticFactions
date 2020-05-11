<?php

declare(strict_types = 1);

namespace core\command\utils;

use core\Cryptic;
use pocketmine\command\CommandSender;

abstract class Command extends \pocketmine\command\Command {

    /** @var SubCommand[] */
    private $subCommands;

    /**
     * @return Cryptic
     */
    public function getCore(): Cryptic {
        return Cryptic::getInstance();
    }

    /**
     * @param SubCommand $subCommand
     */
    public function addSubCommand(SubCommand $subCommand): void {
        $this->subCommands[$subCommand->getName()] = $subCommand;
        foreach($subCommand->getAliases() as $alias) {
            $this->subCommands[$alias] = $subCommand;
        }
    }

    /**
     * @param string $name
     *
     * @return SubCommand|null
     */
    public function getSubCommand(string $name): ?SubCommand {
        return $this->subCommands[$name] ?? null;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    abstract public function execute(CommandSender $sender, string $commandLabel, array $args): void;
}
