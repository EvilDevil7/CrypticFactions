<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\Cryptic;
use core\CrypticPlayer;
use libs\form\MenuForm;
use libs\form\MenuOption;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class QuestListForm extends MenuForm {

    /**
     * QuestListForm constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Active Quests";
        $text = "Which quest would you like to start?";
        $options = [];
        foreach(Cryptic::getInstance()->getQuestManager()->getActiveQuests() as $quest) {
            $options[] = new MenuOption($quest->getName());
        }
        parent::__construct($title, $text, $options);
    }

    /**
     * @param Player $player
     * @param int $selectedOption
     */
    public function onSubmit(Player $player, int $selectedOption): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $option = $this->getOption($selectedOption);
        $player->sendForm(new QuestInfoForm($player, $option->getText()));
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player): void {
        $player->sendForm(new QuestMainForm());
    }
}