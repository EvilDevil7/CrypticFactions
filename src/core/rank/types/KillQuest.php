<?php

declare(strict_types = 1);

namespace core\quest\types;

use core\Cryptic;
use core\CrypticPlayer;
use core\quest\Quest;
use core\translation\Translation;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;

class KillQuest extends Quest {

    /**
     * SellQuest constructor.
     *
     * @param string $name
     * @param string $description
     * @param int $targetValue
     * @param int $difficulty
     */
    public function __construct(string $name, string $description, int $targetValue, int $difficulty) {
        $callable = function(EntityDamageByEntityEvent $event) {
            $player = $event->getEntity();
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            $killer = $event->getDamager();
            if(!$killer instanceof CrypticPlayer) {
                return;
            }
            if($player->getHealth() > $event->getFinalDamage()) {
                return;
            }
            $session = Cryptic::getInstance()->getQuestManager()->getSession($killer);
            if($session->getQuestProgress($this) === -1) {
                return;
            }
            $session->updateQuestProgress($this);
            if($session->getQuestProgress($this) >= $this->targetValue) {
                $player->addQuestPoints($this->getDifficulty());
                $player->sendMessage(Translation::getMessage("questComplete", [
                    "name" => TextFormat::YELLOW . $this->name,
                    "amount" => TextFormat::LIGHT_PURPLE . $this->difficulty
                ]));
            }
        };
        parent::__construct($name, $description, self::KILL, $targetValue, $difficulty, $callable);
    }
}