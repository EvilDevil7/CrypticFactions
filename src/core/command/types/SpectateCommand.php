<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;

class SpectateCommand extends Command {

    /**
     * SpectateCommand constructor.
     */
    public function __construct() {
        parent::__construct("spectate", "Spectate a player.", "/spectate <on/off> [player}");
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
                if(!$sender->isOp()) {
                    if(!$sender->hasPermission("permission.staff")) {
                        $sender->sendMessage(Translation::getMessage("noPermission"));
                        return;
                    }
                }
                switch($args[0]) {
                    case "on":
                        if(!isset($args[1])) {
                            $sender->sendMessage(Translation::getMessage("usageMessage", [
                                "usage" => $this->getUsage()
                            ]));
                            return;
                        }
                        $player = $this->getCore()->getServer()->getPlayer($args[1]);
                        if($player === null) {
                            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                            return;
                        }
						$pk = new GameRulesChangedPacket();
						$pk->gameRules = [
							"showcoordinates" => [
								1,
								false
							]
						];
						$sender->sendDataPacket($pk);
                        $sender->teleport($player);
                        $sender->setGamemode(CrypticPlayer::SPECTATOR);
                        break;
                    case "off":
                        if($sender->getGamemode() === CrypticPlayer::SPECTATOR) {
                            $sender->setGamemode(CrypticPlayer::SURVIVAL);
                            $sender->teleport($this->getCore()->getServer()->getDefaultLevel()->getSpawnLocation());
                        }
                        break;
                }
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