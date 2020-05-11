<?php

declare(strict_types = 1);

namespace core\announcement\task;

use core\Cryptic;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class BroadcastMessagesTask extends Task {

    /** @var Cryptic */
    private $core;

    /**
     * BroadcastMessagesTask constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @param int $currentTick
     *
     * @throws TranslationException
     */
    public function onRun(int $currentTick) {
        $message = $this->core->getAnnouncementManager()->getNextMessage();
        $this->core->getServer()->broadcastMessage(Translation::getMessage("broadcastMessage", [
            "message" => TextFormat::LIGHT_PURPLE . $message
        ]));
    }
}