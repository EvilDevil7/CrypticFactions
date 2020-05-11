<?php

namespace core\gamble\command\forms;

use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\ModalForm;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CoinFlipConfirmationForm extends ModalForm {

    /** @var CrypticPlayer */
    private $target;

    /**
     * CoinFlipConfirmationForm constructor.
     *
     * @param CrypticPlayer $target
     */
    public function __construct(CrypticPlayer $target) {
        $this->target = $target;
        $amount = $target->getCore()->getGambleManager()->getCoinFlip($target);
        $title = TextFormat::BOLD . TextFormat::YELLOW . "Coin Flip";
        $text = "Are you sure you would like to do a $$amount coin flip with {$target->getName()}?";
        parent::__construct($title, $text);
    }

    /**
     * @param Player $player
     * @param bool $choice
     *
     * @throws TranslationException
     */
    public function onSubmit(Player $player, bool $choice): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($choice == true) {
            if((!$this->target instanceof CrypticPlayer) or (!$this->target->isOnline())) {
                $player->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
            $gambleManager = $player->getCore()->getGambleManager();
            $amount = $gambleManager->getCoinFlip($this->target);
            if($amount === null) {
                $player->sendMessage(Translation::getMessage("invalidPlayer"));
                return;
            }
            if($this->target->getBalance() < $amount) {
                $player->sendMessage(Translation::getMessage("targetNotEnoughMoney", [
                    "name" => TextFormat::RED . $this->target->getName()
                ]));
                return;
            }
            $chance = mt_rand(1, 100);
            $winner = $player;
            $loser = $this->target;
            if($chance > 50) {
                $winner = $this->target;
                $loser = $player;
            }
            $gambleManager->addWin($winner);
            $gambleManager->addLoss($loser);
            $gambleManager->getRecord($winner, $wins, $losses);
            $gambleManager->getRecord($loser, $wins2, $losses2);
            $winTotal = $amount * 2;
            $player->getServer()->broadcastMessage(TextFormat::GREEN . $winner->getName() . TextFormat::DARK_GRAY . " ($wins-$losses)" . TextFormat::GRAY . " has defeated " . TextFormat::RED . $loser->getName() . TextFormat::DARK_GRAY . " ($wins2-$losses2) " . TextFormat::GRAY . "in a " . TextFormat::LIGHT_PURPLE . "$$winTotal" . TextFormat::GRAY . " coin flip!");
            $winner->addToBalance($amount);
            $loser->subtractFromBalance($amount);
            $gambleManager->removeCoinFlip($this->target);
        }
        return;
    }
}
