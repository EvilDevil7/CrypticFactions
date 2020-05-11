<?php

declare(strict_types = 1);

namespace core\faction;

use core\Cryptic;
use pocketmine\level\format\Chunk;

class Claim {

    /** @var Chunk */
    private $chunk;

    /** @var Faction */
    private $faction;

    /**
     * Claim constructor.
     *
     * @param int $chunkX
     * @param int $chunkZ
     * @param Faction $faction
     */
    public function __construct(int $chunkX, int $chunkZ, Faction $faction) {
        $level = Cryptic::getInstance()->getServer()->getLevelByName(Faction::CLAIM_WORLD);
        $this->chunk = $level->getChunk($chunkX, $chunkZ);
        $this->faction = $faction;
    }

    /**
     * @return Chunk
     */
    public function getChunk(): Chunk {
        return $this->chunk;
    }

    /**
     * @return Faction
     */
    public function getFaction(): Faction {
        return $this->faction;
    }

    /**
     * @param Faction $faction
     */
    public function setFaction(Faction $faction): void {
        $this->faction = $faction;
    }
}
