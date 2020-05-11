<?php

declare(strict_types = 1);

namespace core\kit\types;

use core\item\types\HolyBox;
use core\kit\Kit;
use core\kit\KitManager;

class SaintKit extends Kit {

    /**
     * Saint constructor.
     *
     * @param KitManager $manager
     */
    public function __construct(KitManager $manager) {
        $kits = $manager->getSacredKits();
        $kit = $kits[array_rand($kits)];
        $items =  [
            (new HolyBox($kit))->getItemForm()
        ];
        parent::__construct("Saint", self::MYTHIC, $items, 864000);
    }
}