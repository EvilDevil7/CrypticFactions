<?php

declare(strict_types = 1);

namespace core\crate;

use core\crate\types\RareCrate;
use core\crate\types\EpicCrate;
use core\crate\types\LegendaryCrate;
use core\crate\types\TagCrate;
use core\crate\types\CommonCrate;
use core\crate\types\VoteCrate;
use core\Cryptic;
use pocketmine\level\Position;

class CrateManager {

    /** @var Cryptic */
    private $core;

    /** @var Crate[] */
    private $crates = [];

    /**
     * CrateManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getServer()->getPluginManager()->registerEvents(new CrateListener($core), $core);
        $this->init();
    }

    public function init() {
        $level = $this->core->getServer()->getDefaultLevel();
        $this->addCrate(new CommonCrate(new Position(225, 73, 283, $level)));
        $this->addCrate(new LegendaryCrate(new Position(233, 73, 278, $level)));
        $this->addCrate(new TagCrate(new Position(241, 73, 274, $level)));
        $this->addCrate(new EpicCrate(new Position(249, 73, 270, $level)));
        $this->addCrate(new RareCrate(new Position(258, 73, 268, $level)));
        $this->addCrate(new VoteCrate(new Position(217, 73, 289, $level)));
    }

    /**
     * @return Crate[]
     */
    public function getCrates(): array {
        return $this->crates;
    }

    /**
     * @param string $identifier
     *
     * @return Crate|null
     */
    public function getCrate(string $identifier): ?Crate {
        return isset($this->crates[strtolower($identifier)]) ? $this->crates[strtolower($identifier)] : null;
    }

    /**
     * @param Crate $crate
     */
    public function addCrate(Crate $crate) {
        $this->crates[strtolower($crate->getName())] = $crate;
    }
}