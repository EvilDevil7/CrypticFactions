<?php

declare(strict_types = 1);

namespace core\event;

use core\event\christmas\PresentGiftChooser;
use core\Cryptic;

class EventManager {

    /** @var Cryptic */
    private $core;

    /** @var PresentGiftChooser */
    private static $giftChooser;

    /**
     * ItemManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        self::$giftChooser = new PresentGiftChooser();
    }

    /**
     * @return PresentGiftChooser
     */
    public static function getGiftChooser(): PresentGiftChooser {
        return self::$giftChooser;
    }
}
