<?php

declare(strict_types = 1);

namespace core\watchdog;

use core\Cryptic;

class WatchdogManager {

    /** @var Cryptic */
    private $core;

    /**
     * WatchdogManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getServer()->getPluginManager()->registerEvents(new WatchdogListener($core), $core);
    }
}
