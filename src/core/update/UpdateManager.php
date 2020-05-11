<?php

declare(strict_types = 1);

namespace core\update;

use core\Cryptic;
use core\CrypticPlayer;
use core\update\task\UpdateTask;
use libs\utils\UtilsException;
use pocketmine\item\Armor;
use pocketmine\utils\TextFormat;

class UpdateManager {

    /** @var Cryptic */
    private $core;

    /**
     * UpdateManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getScheduler()->scheduleRepeatingTask(new UpdateTask($core), 1);
    }

    /**
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function updateScoreboard(CrypticPlayer $player): void {
        $scoreboard = $player->getScoreboard();
        if($scoreboard === null) {
            return;
        }
        if($scoreboard->isSpawned() === false) {
            $scoreboard->spawn(Cryptic::SERVER_NAME);
            return;
        }
        if($scoreboard->getLine(1) === null) {
            $scoreboard->setScoreLine(1, "");
        }
        $scoreboard->setScoreLine(2, $player->getRank()->getColoredName() . TextFormat::RESET . TextFormat::WHITE . " " . $player->getName());
        if($scoreboard->getLine(3) === null) {
            $scoreboard->setScoreLine(3, "");
        }
        if($player->isUsingPVPHUD() === false) {
            $scoreboard->setScoreLine(4, TextFormat::RESET . TextFormat::YELLOW . "Kills: " . TextFormat::RESET . TextFormat::WHITE . $player->getKills());
            $scoreboard->setScoreLine(5, TextFormat::RESET . TextFormat::YELLOW . "Balance: " . TextFormat::RESET . TextFormat::WHITE . "$" . number_format($player->getBalance()));
            $scoreboard->setScoreLine(6, TextFormat::RESET . TextFormat::YELLOW . "Lucky Blocks: " . TextFormat::RESET . TextFormat::WHITE . $player->getLuckyBlocksMined());
            if($scoreboard->getLine(7) === null) {
                $scoreboard->setScoreLine(7, "");
            }
            if($scoreboard->getLine(8) === null) {
                $scoreboard->setScoreLine(8, TextFormat::RESET . TextFormat::AQUA . "store.crypticpe.net");
            }
            if($scoreboard->getLine(9) === null) {
                $scoreboard->setScoreLine(9, TextFormat::RESET . TextFormat::AQUA . "vote.crypticpe.net");
            }
            return;
        }
        $helmet = $player->getArmorInventory()->getHelmet();
        $durability = "Not detected.";
        if($helmet instanceof Armor) {
            $durability = $helmet->getMaxDurability() - $helmet->getDamage();
        }
        $scoreboard->setScoreLine(4, TextFormat::RESET . TextFormat::DARK_RED . "Helmet: " . TextFormat::RESET . TextFormat::WHITE . $durability);
        $chestplate = $player->getArmorInventory()->getChestplate();
        $durability = "Not detected.";
        if($chestplate instanceof Armor) {
            $durability = $chestplate->getMaxDurability() - $chestplate->getDamage();
        }
        $scoreboard->setScoreLine(5, TextFormat::RESET . TextFormat::DARK_RED . "Chestplate: " . TextFormat::RESET . TextFormat::WHITE . $durability);
        $leggings = $player->getArmorInventory()->getLeggings();
        $durability = "Not detected.";
        if($leggings instanceof Armor) {
            $durability = $leggings->getMaxDurability() - $leggings->getDamage();
        }
        $scoreboard->setScoreLine(6, TextFormat::RESET . TextFormat::DARK_RED . "Leggings: " . TextFormat::RESET . TextFormat::WHITE . $durability);
        $boots = $player->getArmorInventory()->getBoots();
        $durability = "Not detected.";
        if($boots instanceof Armor) {
            $durability = $boots->getMaxDurability() - $boots->getDamage();
        }
        $scoreboard->setScoreLine(7, TextFormat::RESET . TextFormat::DARK_RED . "Boots: " . TextFormat::RESET . TextFormat::WHITE . $durability);
        if($scoreboard->getLine(8) !== "") {
            $scoreboard->setScoreLine(8, "");
        }
        $scoreboard->setScoreLine(9, TextFormat::RESET . TextFormat::YELLOW . "GA CD: " . TextFormat::RESET . TextFormat::WHITE . $this->core->getCombatManager()->getGoldenAppleCooldown($player) . "s");
        $scoreboard->setScoreLine(10, TextFormat::RESET . TextFormat::LIGHT_PURPLE . "EGA CD: " . TextFormat::RESET . TextFormat::WHITE . $this->core->getCombatManager()->getGodAppleCooldown($player) . "s");
        $scoreboard->setScoreLine(11, TextFormat::RESET . TextFormat::DARK_PURPLE . "EP CD: " . TextFormat::RESET . TextFormat::WHITE . $this->core->getCombatManager()->getEnderPearlCooldown($player) . "s");
    }
}
