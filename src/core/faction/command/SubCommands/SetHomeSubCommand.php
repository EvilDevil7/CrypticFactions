<?php

declare(strict_types = 1);

namespace core\faction\command\subCommands;

use core\command\utils\SubCommand;
use core\faction\Faction;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class SetHomeSubCommand extends SubCommand {

    /**
     * SetHomeSubCommand constructor.
     */
    public function __construct() {
        parent::__construct("sethome", "/faction sethome");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $manager = $this->getCore()->getFactionManager();
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if($sender->getFaction() === null) {
            $sender->sendMessage(Translation::getMessage("beInFaction"));
            return;
        }
        if($sender->getFactionRole() !== Faction::LEADER) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $claim = $manager->getClaimInPosition($sender->asPosition());
        if($claim === null or $claim->getFaction()->getName() !== $sender->getFaction()->getName()) {
            $sender->sendMessage(Translation::getMessage("mustBeInClaim"));
            return;
        }
        $sender->getFaction()->setHome($sender->asPosition());
        $sender->sendMessage(Translation::getMessage("homeSet"));
    }
}