<?php

declare(strict_types = 1);

namespace core\clearlag;

use core\clearlag\task\ClearLagTask;
use core\Cryptic;

class ClearLagManager{

    public function __construct(){
        Cryptic::getInstance()->getScheduler()->scheduleRepeatingTask(new ClearLagTask(), 20);
    }
}
