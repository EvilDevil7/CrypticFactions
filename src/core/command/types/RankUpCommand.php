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

class RankUpCommand extends Command {

    /**
     * RankUpCommand constructor.
     */
    public function __construct() {
        parent::__construct("rankup", "Rank up", "/rankup", ["ru"]);
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
        $rank = $sender->getRank();
        switch($rank->getIdentifier()) {
            case Rank::PLAYER:
                $price = 1000000;
                $rankId = Rank::KNIGHT;
                break;
            case Rank::KNIGHT:
                $price = 5000000;
                $rankId = Rank::WIZARD;
                break;
            case Rank::WIZARD:
                $price = 20000000;
                $rankId = Rank::KING;
                break;
            case Rank::KING:
                $price = 100000000;
                $rankId = Rank::MYSTIC;
                break;
            case Rank::MYSTIC:
                $price = 1000000000;
                $rankId = Rank::CRYPTIC;
                break;
            default:
                if(!$sender->hasPermission("permission.knight")) {
                    $price = 1000000;
                    $permission = "permission.knight";
                }
                elseif(!$sender->hasPermission("permission.wizard")) {
                    $price = 5000000;
                    $permission = "permission.wizard";
                }
                elseif(!$sender->hasPermission("permission.king")) {
                    $price = 20000000;
                    $permission = "permission.king";
                }
                elseif(!$sender->hasPermission("permission.mystic")) {
                    $price = 100000000;
                    $permission = "permission.mystic";
                }
                elseif(!$sender->hasPermission("permission.cryptic")) {
                    $price = 1000000000;
                    $permission = "permission.elder";
                }
                else {
                    $price = null;
                    $permission = null;
                }
        }
        if((!isset($price)) or $price === null) {
            $sender->sendMessage(Translation::getMessage("maxRank"));
            return;
        }
        if($price > $sender->getBalance()) {
            $sender->sendMessage(Translation::getMessage("notEnoughMoneyRankUp", [
                "amount" => TextFormat::RED . "$$price"
            ]));
            return;
        }
        if(isset($rankId)) {
            $sender->subtractFromBalance($price);
            $sender->setRank(($rank = $sender->getCore()->getRankManager()->getRankByIdentifier($rankId)));
            $this->getCore()->getServer()->broadcastMessage(Translation::getMessage("rankUp", [
                "name" => TextFormat::AQUA . $sender->getName(),
                "rank" => TextFormat::YELLOW . $rank->getName()
            ]));
        }
        elseif(isset($permission)) {
            $sender->subtractFromBalance($price);
            $sender->getSession()->addPermissions((string)$permission);
            $rank = ucfirst(explode(".", $permission)[1]);
            $this->getCore()->getServer()->broadcastMessage(Translation::getMessage("rankUp", [
                "name" => TextFormat::AQUA . $sender->getName(),
                "rank" => TextFormat::YELLOW . $rank
            ]));
        }
        else {
            $sender->sendMessage(Translation::getMessage("errorOccurred"));
        }
    }
}