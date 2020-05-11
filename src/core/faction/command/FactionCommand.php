<?php

declare(strict_types = 1);

namespace core\faction\command;

use core\command\utils\Command;
use core\faction\command\subCommands\AddPowerSubCommand;
use core\faction\command\subCommands\AllySubCommand;
use core\faction\command\subCommands\AnnounceSubCommand;
use core\faction\command\subCommands\ChatSubCommand;
use core\faction\command\subCommands\ClaimSubCommand;
use core\faction\command\subCommands\CreateSubCommand;
use core\faction\command\subCommands\DemoteSubCommand;
use core\faction\command\subCommands\DepositSubCommand;
use core\faction\command\subCommands\DisbandSubCommand;
use core\faction\command\subCommands\ForceDeleteSubCommand;
use core\faction\command\subCommands\HelpSubCommand;
use core\faction\command\subCommands\HomeSubCommand;
use core\faction\command\subCommands\InfoSubCommand;
use core\faction\command\subCommands\InviteSubCommand;
use core\faction\command\subCommands\JoinSubCommand;
use core\faction\command\subCommands\KickSubCommand;
use core\faction\command\subCommands\LeaderSubCommand;
use core\faction\command\subCommands\LeaveSubCommand;
use core\faction\command\subCommands\MapSubCommand;
use core\faction\command\subCommands\OverClaimSubCommand;
use core\faction\command\subCommands\PromoteSubCommand;
use core\faction\command\subCommands\SetHomeSubCommand;
use core\faction\command\subCommands\TopSubCommand;
use core\faction\command\subCommands\UnallySubCommand;
use core\faction\command\subCommands\UnclaimSubCommand;
use core\faction\command\subCommands\WithdrawSubCommand;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class FactionCommand extends Command {

    /**
     * FactionCommand constructor.
     */
    public function __construct() {
        $this->addSubCommand(new AddPowerSubCommand());
        $this->addSubCommand(new AllySubCommand());
        $this->addSubCommand(new AnnounceSubCommand());
        $this->addSubCommand(new ChatSubCommand());
        $this->addSubCommand(new ClaimSubCommand());
        $this->addSubCommand(new CreateSubCommand());
        $this->addSubCommand(new DemoteSubCommand());
        $this->addSubCommand(new DepositSubCommand());
        $this->addSubCommand(new DisbandSubCommand());
        $this->addSubCommand(new ForceDeleteSubCommand());
        $this->addSubCommand(new HelpSubCommand());
        $this->addSubCommand(new HomeSubCommand());
        $this->addSubCommand(new InfoSubCommand());
        $this->addSubCommand(new InviteSubCommand());
        $this->addSubCommand(new JoinSubCommand());
        $this->addSubCommand(new KickSubCommand());
        $this->addSubCommand(new LeaderSubCommand());
        $this->addSubCommand(new LeaveSubCommand());
        $this->addSubCommand(new MapSubCommand());
        $this->addSubCommand(new OverClaimSubCommand());
        $this->addSubCommand(new PromoteSubCommand());
        $this->addSubCommand(new SetHomeSubCommand());
        $this->addSubCommand(new TopSubCommand());
        $this->addSubCommand(new UnallySubCommand());
        $this->addSubCommand(new UnclaimSubCommand());
        $this->addSubCommand(new WithdrawSubCommand());
        parent::__construct("faction", "Manage faction", "/faction help <1-5>", ["f"]);
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
