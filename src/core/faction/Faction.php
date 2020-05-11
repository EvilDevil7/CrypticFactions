<?php

declare(strict_types = 1);

namespace core\faction;

use core\Cryptic;
use core\CrypticPlayer;
use pocketmine\level\Position;

class Faction {

    const RECRUIT = 0;

    const MEMBER = 1;

    const OFFICER = 2;

    const LEADER = 3;

    const MAX_MEMBERS = 30;

    const MAX_ALLIES = 5;

    const MEMBERS_NEEDED_TO_CLAIM = 3;

    const POWER_PER_KILL = 5;

    const POWER_PER_JOIN = 10;

    const POWER_PER_ALLY = 15;

    const CLAIM_WORLD = "wild";

    /** @var string */
    private $name;

    /** @var string[] */
    private $members = [];

    /** @var string[] */
    private $invites = [];

    /** @var string[] */
    private $allies = [];

    /** @var string[] */
    private $allyRequests = [];

    /** @var int */
    private $balance = 0;

    /** @var int */
    private $strength = 0;

    /** @var null|Position */
    private $home = null;

    /**
     * Faction constructor.
     *
     * @param string $name
     * @param Position|null $home
     * @param array $members
     * @param array $allies
     * @param int $balance
     * @param int $strength
     */
    public function __construct(string $name, ?Position $home, array $members, array $allies, int $balance, int $strength) {
        $this->name = $name;
        $this->home = $home;
        $this->members = $members;
        $this->allies = $allies;
        $this->balance = $balance;
        $this->strength = $strength;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getMembers(): array {
        return $this->members;
    }

    /**
     * @return CrypticPlayer[]
     */
    public function getOnlineMembers(): array {
        $members = [];
        foreach($this->members as $member) {
            $player = Cryptic::getInstance()->getServer()->getPlayer($member);
            if($player !== null) {
                $members[] = $player;
            }
        }
        return $members;
    }

    /**
     * @param string|CrypticPlayer $player
     *
     * @return bool
     */
    public function isInFaction($player): bool {
        $player = $player instanceof CrypticPlayer ? $player->getName() : $player;
        return in_array($player, $this->members);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function demote(CrypticPlayer $player): void {
        $player->setFactionRole($player->getFactionRole() - 1);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function promote(CrypticPlayer $player): void {
        $player->setFactionRole($player->getFactionRole() + 1);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addMember(CrypticPlayer $player): void {
        $this->members[] = $player->getName();
        $player->setFactionRole(self::RECRUIT);
        $player->setFaction($this);
        $members = implode(",", $this->members);
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET members = ? WHERE name = ?");
        $stmt->bind_param("ss", $members, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param string|CrypticPlayer $player
     */
    public function removeMember($player): void {
        $name = $player instanceof CrypticPlayer ? $player->getName() : $player;
        unset($this->members[array_search($name, $this->members)]);
        if($player instanceof CrypticPlayer) {
            $player->setFaction(null);
            $player->setFactionRole(null);
        }
        $members = implode(",", $this->members);
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET members = ? WHERE name = ?");
        $stmt->bind_param("ss", $members, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return bool
     */
    public function isInvited(CrypticPlayer $player): bool {
        return in_array($player->getName(), $this->invites);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addInvite(CrypticPlayer $player): void {
        $this->invites[] = $player->getName();
    }

    /**
     * @param CrypticPlayer $player
     */
    public function removeInvite(CrypticPlayer $player): void {
        unset($this->invites[array_search($player->getName(), $this->invites)]);
    }

    /**
     * @param Faction $faction
     *
     * @return bool
     */
    public function isAllying(Faction $faction): bool {
        return in_array($faction->getName(), $this->allyRequests);
    }

    /**
     * @param Faction $faction
     */
    public function addAllyRequest(Faction $faction): void {
        $this->allyRequests[] = $faction->getName();
    }

    /**
     * @param Faction $faction
     */
    public function addAlly(Faction $faction): void {
        $this->allies[] = $faction->getName();
        $allies = implode(",", $this->allies);
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET allies = ? WHERE name = ?");
        $stmt->bind_param("ss", $allies, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Faction $faction
     */
    public function removeAlly(Faction $faction): void {
        unset($this->allies[array_search($faction->getName(), $this->allies)]);
        $allies = implode(",", $this->allies);
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET allies = ? WHERE name = ?");
        $stmt->bind_param("ss", $allies, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return array
     */
    public function getAllies(): array {
        return $this->allies;
    }

    /**
     * @param Faction $faction
     *
     * @return bool
     */
    public function isAlly(Faction $faction): bool {
        return in_array($faction->getName(), $this->allies);
    }

    /**
     * @param int $amount
     */
    public function addMoney(int $amount): void {
        $this->balance += $amount;
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET balance = balance + ? WHERE name = ?");
        $stmt->bind_param("is", $amount, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param int $amount
     */
    public function subtractMoney(int $amount): void {
        $this->balance -= $amount;
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET balance = balance - ? WHERE name = ?");
        $stmt->bind_param("is", $amount, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int
     */
    public function getBalance(): int {
        return $this->balance;
    }

    /**
     * @param int $amount
     */
    public function addStrength(int $amount): void {
        $this->strength += $amount;
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET strength = strength + ? WHERE name = ?");
        $stmt->bind_param("is", $amount, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param int $amount
     */
    public function subtractStrength(int $amount): void {
        $this->strength -= $amount;
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET strength = strength - ? WHERE name = ?");
        $stmt->bind_param("is", $amount, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int
     */
    public function getStrength(): int {
        return $this->strength;
    }

    /**
     * @param Position|null $position
     */
    public function setHome(?Position $position = null): void {
        $this->home = $position;
        $x = null;
        $y = null;
        $z = null;
        $level = null;
        if($position !== null) {
            $x = $position->getX();
            $y = $position->getY();
            $z = $position->getZ();
        }
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("UPDATE factions SET x = ?, y = ?, z = ? WHERE name = ?");
        $stmt->bind_param("iiis", $x, $y, $z, $this->name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return Position|null
     */
    public function getHome(): ?Position {
        return $this->home;
    }
}