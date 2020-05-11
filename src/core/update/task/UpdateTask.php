<?php

declare(strict_types = 1);

namespace core\update\task;

use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\UtilsException;
use pocketmine\scheduler\Task;

class UpdateTask extends Task {

    /** @var Cryptic */
    private $core;

    /** @var CrypticPlayer[] */
    private $players = [];

    /**
     * UpdateTask constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->players = $core->getServer()->getOnlinePlayers();
    }

    /**
     * @param int $tick
     *
     * @throws UtilsException
     */
    public function onRun(int $tick) {
        if(empty($this->players)) {
            $this->players = $this->core->getServer()->getOnlinePlayers();
        }
        $player = array_shift($this->players);
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($player->isOnline() === false) {
            return;
        }
        $this->core->getUpdateManager()->updateScoreboard($player);
    }
}
