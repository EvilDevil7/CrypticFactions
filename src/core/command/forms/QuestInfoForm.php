<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\Cryptic;
use core\CrypticPlayer;
use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class QuestInfoForm extends CustomForm {

    /**
     * QuestInfoForm constructor.
     *
     * @param CrypticPlayer $player
     * @param string        $quest
     */
    public function __construct(CrypticPlayer $player, string $quest) {
        $title = TextFormat::BOLD . TextFormat::AQUA . $quest;
        $quest = Cryptic::getInstance()->getQuestManager()->getQuest($quest);
        $session = Cryptic::getInstance()->getQuestManager()->getSession($player);
        $elements = [];
        $elements[] = new Label("Description", "Description: " . $quest->getDescription());
        $progress = $session->getQuestProgress($quest);
        $target = $quest->getTargetValue();
        if($progress === -1) {
            $progress = $target;
        }
        $elements[] = new Label("Progress", "Progress: $progress/$target");
        $elements[] = new Label("Difficulty", "Difficulty: " . $quest->getDifficultyName());
        $elements[] = new Label("Reward", "Reward: " . $quest->getDifficulty() . " quest points");
        parent::__construct($title, $elements);
    }

    /**
     * @param Player $player
     */
    public function onClose(Player $player): void {
        $player->sendForm(new QuestListForm());
    }
}