<?php

declare(strict_types = 1);

namespace core\trade;

use core\Cryptic;
use core\trade\task\TradeHeartbeatTask;

class TradeManager {

    /** @var Cryptic */
    private $core;

    /** @var TradeSession[] */
    private $sessions = [];

    /**
     * TradeManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getScheduler()->scheduleRepeatingTask(new TradeHeartbeatTask($this), 20);
    }

    /**
     * @param TradeSession $session
     */
    public function addSession(TradeSession $session): void {
        $this->sessions[] = $session;
    }

    /**
     * @param int $key
     */
    public function removeSession(int $key): void {
        if(isset($this->sessions[$key])) {
            unset($this->sessions[$key]);
        }
    }

    /**
     * @return TradeSession[]
     */
    public function getSessions(): array {
        return $this->sessions;
    }
}
