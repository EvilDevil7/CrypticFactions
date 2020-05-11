<?php

/**
 * @stamp Part of DaRealPandaz and Heisenberg Rewrite
 */

namespace core\crate;

class CratePlayerData {

    /** @var int */
    private $rare = 0;
    /** @var int */
    private $legendary = 0;
    /** @var int */
    private $mythic = 0;
    /** @var int */
    private $ultra = 0;

    function __construct(int $rare = 0, int $legendary = 0, int $mythic = 0, int $ultra = 0) {
        $this->rare = $rare;
        $this->legendary = $legendary;
        $this->mythic = $mythic;
        $this->ultra = $ultra;
    }

    /**
     * @return int
     */
    function getRare(): int {
        return $this->rare;
    }

    /**
     * @param int $amount
     */
    function addRare(int $amount) {
        $this->rare += $amount;
    }

    /**
     * @param int $amount
     */
    function subtractRare(int $amount) {
        $this->rare -= $amount;
    }

    /**
     * @param int $amount
     */
    function setRare(int $amount) {
        $this->rare = $amount;
    }

    /**
     * @return int
     */
    function getLegendary(): int {
        return $this->legendary;
    }

    /**
     * @param int $amount
     */
    function addLegendary(int $amount) {
        $this->legendary += $amount;
    }

    /**
     * @param int $amount
     */
    function subtractLegendary(int $amount) {
        $this->legendary -= $amount;
    }

    /**
     * @param int $amount
     */
    function setLegendary(int $amount) {
        $this->legendary = $amount;
    }

    /**
     * @return int
     */
    function getMythic(): int {
        return $this->mythic;
    }

    /**
     * @param int $amount
     */
    function addMythic(int $amount) {
        $this->mythic += $amount;
    }

    /**
     * @param int $amount
     */
    function subtractMythic(int $amount) {
        $this->mythic -= $amount;
    }

    /**
     * @param int $amount
     */
    function setMythic(int $amount) {
        $this->mythic = $amount;
    }

    /**
     * @return int
     */
    function getUltra(): int {
        return $this->ultra;
    }

    /**
     * @param int $amount
     */
    function addUltra(int $amount) {
        $this->ultra += $amount;
    }

    /**
     * @param int $amount
     */
    function subtractUltra(int $amount) {
        $this->ultra -= $amount;
    }

    /**
     * @param int $amount
     */
    function setUltra(int $amount) {
        $this->ultra = $amount;
    }

}