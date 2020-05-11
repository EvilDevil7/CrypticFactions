<?php

namespace core\gamble;

use core\Cryptic;
use core\CrypticPlayer;

class GambleManager {

    /** @var Cryptic */
    private $core;

    /** @var int[] */
    private $coinFlips = [];

    /** @var string[] */
    private $coinFlipRecord = [];

    /**
     * GambleManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->core->getServer()->getPluginManager()->registerEvents(new GambleListener($core), $core);
    }

    /**
     * @return int[]
     */
    public function getCoinFlips(): array {
        return $this->coinFlips;
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return int|null
     */
    public function getCoinFlip(CrypticPlayer $player): ?int {
        return $this->coinFlips[$player->getName()] ?? null;
    }

    /**
     * @param CrypticPlayer $player
     * @param int           $amount
     */
    public function addCoinFlip(CrypticPlayer $player, int $amount): void {
        if(isset($this->coinFlips[$player->getName()])) {
            return;
        }
        $this->coinFlips[$player->getName()] = $amount;
    }

    /**
     * @param CrypticPlayer $player
     */
    public function removeCoinFlip(CrypticPlayer $player): void {
        if(!isset($this->coinFlips[$player->getName()])) {
            return;
        }
        unset($this->coinFlips[$player->getName()]);
    }

    /**
     * @param CrypticPlayer $player
     * @param $wins
     * @param $losses
     */
    public function getRecord(CrypticPlayer $player, &$wins, &$losses): void {
        $record = $this->coinFlipRecord[$player->getName()];
        $reward = explode(":", $record);
        $wins = $reward[0];
        $losses = $reward[1];
    }

    /**
     * @param CrypticPlayer $player
     */
    public function createRecord(CrypticPlayer $player): void {
        $this->coinFlipRecord[$player->getName()] = "0:0";
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addWin(CrypticPlayer $player): void {
        if(!isset($this->coinFlipRecord[$player->getName()])){
            $this->coinFlipRecord[$player->getName()] = "0:0";
        }
        $record = $this->coinFlipRecord[$player->getName()];
        $reward = explode(":", $record);
        $wins = intval($reward[0]) + 1;
        $losses = $reward[1];
        $this->coinFlipRecord[$player->getName()] = "$wins:$losses";
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addLoss(CrypticPlayer $player): void {
        $record = $this->coinFlipRecord[$player->getName()];
        $reward = explode(":", $record);
        $wins = $reward[0];
        $losses = $reward[1] + 1;
        $this->coinFlipRecord[$player->getName()] = "$wins:$losses";
    }
}
