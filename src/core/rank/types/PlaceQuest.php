<?php

declare(strict_types = 1);

namespace core\quest\types;

use core\Cryptic;
use core\CrypticPlayer;
use core\quest\Quest;
use core\translation\Translation;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\utils\TextFormat;

class PlaceQuest extends Quest {

    /** @var int */
    private $block;

    /**
     * PlaceQuest constructor.
     *
     * @param string $name
     * @param string $description
     * @param int $targetValue
     * @param int $difficulty
     * @param int $blockId
     */
    public function __construct(string $name, string $description, int $targetValue, int $difficulty, int $blockId) {
        $this->block = $blockId;
        $callable = function(BlockPlaceEvent $event) {
            $block = $event->getBlock();
            $player = $event->getPlayer();
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            if($block->getId() === $this->block) {
                $session = Cryptic::getInstance()->getQuestManager()->getSession($player);
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
            }
        };
        parent::__construct($name, $description, self::PLACE, $targetValue, $difficulty, $callable);
    }
}