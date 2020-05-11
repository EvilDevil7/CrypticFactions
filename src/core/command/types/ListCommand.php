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

class ListCommand extends Command {

    /**
     * ListCommand constructor.
     */
    public function __construct() {
        parent::__construct("list", "List current online players.");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        $players = [];
        $rankedPlayers = [];
        $staffs = [];
        $youtubers = [];
        foreach($this->getCore()->getServer()->getOnlinePlayers() as $player) {
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            if($player->getRank()->getIdentifier() >= Rank::PLAYER and $player->getRank()->getIdentifier() <= Rank::MYSTIC) {
                $players[] = $player->getRank()->getIdentifier() === Rank::PLAYER ? TextFormat::WHITE . $player->getName() : $player->getRank()->getColoredName() . " " . TextFormat::WHITE . $player->getName();
                continue;
            }
            if($player->getRank()->getIdentifier() >= Rank::GOD and $player->getRank()->getIdentifier() <= Rank::WARLORD) {
                $rankedPlayers[] = $player->getRank()->getColoredName() . " " . TextFormat::WHITE . $player->getName();
                continue;
            }
            if($player->getRank()->getIdentifier() === Rank::OVERLORD) {
                $rankedPlayers[] = $player->getRank()->getColoredName() . " " . TextFormat::WHITE . $player->getName();
                continue;
            }
            if($player->getRank()->getIdentifier() === Rank::YOUTUBER or $player->getRank()->getIdentifier() === Rank::FAMOUS) {
                $youtubers[] = $player->getRank()->getColoredName() . " " . TextFormat::WHITE . $player->getName();
                continue;
            }
            else {
                $staffs[] = $player->getRank()->getColoredName() . " " . TextFormat::WHITE . $player->getName();
            }
        }
        $onlinePlayers = count($this->getCore()->getServer()->getOnlinePlayers());
        $times = 0;
        if($onlinePlayers >= 1){
            $times = (int)round((count($players) / $onlinePlayers) * 20);
        }
        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::GOLD . str_repeat("|", $times) . TextFormat::GRAY . str_repeat("|", 20 - $times) . TextFormat::DARK_GRAY . "] " . Translation::getMessage("listMessage", [
                "group" => TextFormat::GOLD . "Players",
                "count" => TextFormat::DARK_GRAY . "(" . TextFormat::BOLD . TextFormat::GOLD . count($players) . TextFormat::RESET . TextFormat::DARK_GRAY . ")",
                "list" => TextFormat::WHITE . implode(", ", $players)
            ]));
        if($onlinePlayers >= 1){
            $times = (int)round((count($rankedPlayers) / $onlinePlayers) * 20);
        }
        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::YELLOW . str_repeat("|", $times) . TextFormat::GRAY . str_repeat("|", 20 - $times) . TextFormat::DARK_GRAY . "] " . Translation::getMessage("listMessage", [
                "group" => TextFormat::YELLOW . "Ranked Players",
                "count" => TextFormat::DARK_GRAY . "(" . TextFormat::BOLD . TextFormat::YELLOW . count($rankedPlayers) . TextFormat::RESET . TextFormat::DARK_GRAY . ")",
                "list" => TextFormat::WHITE . implode(", ", $rankedPlayers)
            ]));
        if($onlinePlayers >= 1){
            $times = (int)round((count($youtubers) / $onlinePlayers) * 20);
        }
        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::WHITE . str_repeat("|", $times) . TextFormat::GRAY . str_repeat("|", 20 - $times) . TextFormat::DARK_GRAY . "] " . Translation::getMessage("listMessage", [
                "group" => TextFormat::WHITE . "You" . TextFormat::RED . "Tubers",
                "count" => TextFormat::DARK_GRAY . "(" . TextFormat::BOLD . TextFormat::RED . count($youtubers) . TextFormat::RESET . TextFormat::DARK_GRAY . ")",
                "list" => TextFormat::WHITE . implode(", ", $youtubers)
            ]));
        if($onlinePlayers >= 1){
            $times = (int)round((count($staffs) / $onlinePlayers) * 20);
        }
        $sender->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::LIGHT_PURPLE . str_repeat("|", $times) . TextFormat::GRAY . str_repeat("|", 20 - $times) . TextFormat::DARK_GRAY . "] " . Translation::getMessage("listMessage", [
                "group" => TextFormat::LIGHT_PURPLE . "Staffs",
                "count" => TextFormat::DARK_GRAY . "(" . TextFormat::BOLD . TextFormat::LIGHT_PURPLE . count($staffs) . TextFormat::RESET . TextFormat::DARK_GRAY . ")",
                "list" => TextFormat::WHITE . implode(", ", $staffs)
            ]));
        $sender->sendMessage(TextFormat::DARK_AQUA . "There is a total of " . TextFormat::AQUA . $onlinePlayers . TextFormat::DARK_AQUA . " online player(s).");
    }
}