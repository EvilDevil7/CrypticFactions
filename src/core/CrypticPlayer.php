<?php

namespace core;

use core\data\Naddy;
use pocketmine\Player;

class CrypticPlayer {

    /** @var Player */
    private $player;
    /** @var Naddy */
    private $crypticData;

    public function __construct(Player $player, Naddy $data)
    {
        $this->player = $player;
        $this->crypticData = $data;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return Naddy
     */
    public function getCrypticData(): Naddy
    {
        return $this->crypticData;
    }

}