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

class TeleportAskCommand extends Command {

    /**
     * TeleportAskCommand constructor.
     */
    public function __construct() {
        parent::__construct("teleportask", "Ask to teleport to someone.", "/teleportask <ask/accept/deny> <player>", ["tpa"]);
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
		if($sender->isInStaffMode()){
			$sender->sendMessage("§l§8(§a!§8)§r §cYou can not use this while in staff mode");
			return;
		}
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $player = $this->getCore()->getServer()->getPlayer($args[1]);
        if(!$player instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("invalidPlayer"));
            return;
        }
        if($sender->isTeleporting() === true) {
            $sender->sendMessage(Translation::getMessage("alreadyTeleporting", [
                "name" => "You are"
            ]));
            return;
        }
        if($player->isTeleporting() === true) {
            $sender->sendMessage(Translation::getMessage("alreadyTeleporting", [
                "name" => "{$player->getName()} is"
            ]));
            return;
        }
        switch($args[0]) {
            case "ask":
                if($sender->isRequestingTeleport($player)) {
                    $sender->sendMessage(Translation::getMessage("alreadyRequest"));
                    return;
                }
                $sender->addTeleportRequest($player);
                $sender->sendMessage(Translation::getMessage("requestTeleport", [
                    "name" => "You have",
                    "player" => TextFormat::YELLOW . $player->getName()
                ]));
                $player->sendMessage(Translation::getMessage("requestTeleport", [
                    "name" => TextFormat::YELLOW . $sender->getName() . TextFormat::GRAY . " has",
                    "player" => "you"
                ]));
                break;
            case "accept":
                if(!$player->isRequestingTeleport($sender)) {
                    $sender->sendMessage(Translation::getMessage("didNotRequest"));
                    return;
                }
                $player->removeTeleportRequest($sender);
                $player->sendMessage(Translation::getMessage("acceptRequest"));
                $this->getCore()->getScheduler()->scheduleRepeatingTask(new TeleportTask($player, $sender, 5), 20);
                break;
            case "deny":
                if(!$player->isRequestingTeleport($sender)) {
                    $sender->sendMessage(Translation::getMessage("didNotRequest"));
                    return;
                }
                $player->removeTeleportRequest($sender);
                $player->sendMessage(Translation::getMessage("denyRequest"));
                break;
            default:
                $sender->sendMessage(Translation::getMessage("usageMessage", [
                    "usage" => $this->getUsage()
                ]));
                break;
        }
    }
}