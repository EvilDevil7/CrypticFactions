<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\rank\Rank;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetRankCommand extends Command {

    /**
     * SetRankCommand constructor.
     */
    public function __construct() {
        parent::__construct("setrank", "Set a player's rank.", "/setrank <player> <group>", ["setgroup"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[1])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $player = $this->getCore()->getServer()->getPlayer($args[0]);
        if(!$player instanceof CrypticPlayer) {
            $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("SELECT rankId FROM players WHERE username = ?");
            $stmt->bind_param("s", $args[0]);
            $stmt->execute();
            $stmt->bind_result($rankId);
            $stmt->fetch();
            $stmt->close();
            if($rankId === null) {
                $sender->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
        }
        $rank = $this->getCore()->getRankManager()->getRankByName($args[1]);
        if(!$rank instanceof Rank) {
            $sender->sendMessage(Translation::getMessage("invalidRank"));
            $sender->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "RANKS:");
            $sender->sendMessage(TextFormat::WHITE . implode(", ", $this->getCore()->getRankManager()->getRanks()));
            return;
        }
        if(isset($rankId)) {
            $id = $rank->getIdentifier();
            $stmt = $this->getCore()->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET rankId = ? WHERE username = ?");
            $stmt->bind_param("is", $id, $args[0]);
            $stmt->execute();
            $stmt->close();
        }
        else {
            $player->setRank($rank);
        }
        $sender->sendMessage(Translation::getMessage("rankSet", [
            "rank" => $rank->getColoredName(),
            "name" => TextFormat::GOLD . $player instanceof CrypticPlayer ? $player->getName() : $args[0]
        ]));
    }
}