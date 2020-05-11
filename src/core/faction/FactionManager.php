<?php

declare(strict_types = 1);

namespace core\faction;

use core\Cryptic;
use core\CrypticPlayer;
use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;

class FactionManager {

    /** @var Cryptic */
    private $core;

    /** @var Faction[] */
    private $factions = [];

    /** @var Claim[] */
    private $claims = [];

    /**
     * FactionManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->init();
        $core->getServer()->getPluginManager()->registerEvents(new FactionListener($core), $core);
    }

    public function init(): void {
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT name, x, y, z, members, allies, balance, strength FROM factions");
        $stmt->execute();
        $stmt->bind_result($name, $x, $y, $z, $members, $allies, $balance, $strength);
        while($stmt->fetch()) {
            $home = null;
            if($x !== null and $y !== null and $z !== null) {
                $home = new Position($x, $y, $z, Cryptic::getInstance()->getServer()->getLevelByName(Faction::CLAIM_WORLD));
            }
            $members = explode(",", $members);
            $allyList = [];
            if($allies !== null) {
                $allyList = explode(",", $allies);
            }
            $faction = new Faction($name, $home, $members, $allyList, $balance, $strength);
            $this->factions[$name] = $faction;
        }
        $stmt->close();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT faction, chunkX, chunkZ FROM claims");
        $stmt->execute();
        $stmt->bind_result($fac, $chunkX, $chunkZ);
        while($stmt->fetch()) {
            $hash = Level::chunkHash($chunkX, $chunkZ);
            $this->claims[$hash] = new Claim($chunkX, $chunkZ, $this->factions[$fac]);
        }
        $stmt->close();
    }

    /**
     * @return Faction[]
     */
    public function getFactions(): array {
        return $this->factions;
    }

    /**
     * @param string $name
     *
     * @return Faction|null
     */
    public function getFaction(string $name): ?Faction {
        return $this->factions[$name] ?? null;
    }

    /**
     * @param string        $name
     * @param CrypticPlayer $leader
     *
     * @throws FactionException
     */
    public function createFaction(string $name, CrypticPlayer $leader): void {
        if(isset($this->factions[$name])) {
            throw new FactionException("Unable to override an existing faction!");
        }
        $members = $leader->getName();
        $stmt = Cryptic::getInstance()->getMySQLProvider()->getDatabase()->prepare("INSERT INTO factions(name, members) VALUES(?, ?)");
        $stmt->bind_param("ss", $name, $members);
        $stmt->execute();
        $stmt->close();
        $faction = new Faction($name, null, [$members], [], 0, 100);
        $this->factions[$name] = $faction;
        $leader->setFaction($this->factions[$name]);
        $leader->setFactionRole(Faction::LEADER);
    }

    /**
     * @param string $name
     *
     * @throws FactionException
     */
    public function removeFaction(string $name): void {
        if(!isset($this->factions[$name])) {
            throw new FactionException("Non-existing faction is trying to be removed!");
        }
        $faction = $this->factions[$name];
        unset($this->factions[$name]);
        foreach($faction->getOnlineMembers() as $member) {
            $member->setFaction(null);
            $member->setFactionRole(null);
        }
        foreach($faction->getAllies() as $ally) {
            if(!isset($this->factions[$ally])) {
                continue;
            }
            $this->factions[$ally]->removeAlly($faction);
        }
        foreach($this->claims as $hash => $claim) {
            if($claim->getFaction()->getName() === $name) {
                unset($this->claims[$hash]);
            }
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("DELETE FROM factions WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("DELETE FROM claims WHERE faction = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Claim $claim
     */
    public function addClaim(Claim $claim) {
        $name = $claim->getFaction()->getName();
        $chunkX = $claim->getChunk()->getX();
        $chunkZ = $claim->getChunk()->getZ();
        $this->claims[Level::chunkHash($chunkX, $chunkZ)] = $claim;
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO claims(faction, chunkX, chunkZ) VALUES(?, ?, ?)");
        $stmt->bind_param("sii", $name, $chunkX, $chunkZ);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Claim $claim
     */
    public function removeClaim(Claim $claim) {
        $name = $claim->getFaction()->getName();
        $chunkX = $claim->getChunk()->getX();
        $chunkZ = $claim->getChunk()->getZ();
        unset($this->claims[Level::chunkHash($chunkX, $chunkZ)]);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("DELETE FROM claims WHERE faction = ? AND chunkX = ? AND chunkZ = ?");
        $stmt->bind_param("sii", $name, $chunkX, $chunkZ);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Faction $faction
     * @param Claim $claim
     *
     * @throws FactionException
     */
    public function overClaim(Faction $faction, Claim $claim) {
        $name = $faction->getName();
        $chunkX = $claim->getChunk()->getX();
        $chunkZ = $claim->getChunk()->getZ();
        if(!isset($this->claims[Level::chunkHash($chunkX, $chunkZ)])) {
            throw new FactionException("Invalid claim that's trying to be overclaimed.");
        }
        $this->claims[Level::chunkHash($chunkX, $chunkZ)]->setFaction($faction);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE claims SET faction = ? WHERE chunkX = ? AND chunkZ = ?");
        $stmt->bind_param("sii", $name, $chunkX, $chunkZ);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Position $position
     *
     * @return Claim|null
     */
    public function getClaimInPosition(Position $position): ?Claim {
        if($position->getLevel() === null or $position->getLevel()->getName() !== Faction::CLAIM_WORLD) {
            return null;
        }
        $x = $position->getX();
        $z = $position->getZ();
        $hash = Level::chunkHash($x >> 4, $z >> 4);
        return $this->claims[$hash] ?? null;
    }

    /**
     * @param int $hash
     *
     * @return Claim|null
     */
    public function getClaimByHash(int $hash): ?Claim {
        return $this->claims[$hash] ?? null;
    }

    /**
     * @param CrypticPlayer $player
     */
    public static function sendFactionMap(CrypticPlayer $player): void {
        $chunkX = $player->getX() >> 4;
        $chunkZ = $player->getZ() >> 4;
        $lines = [];
        $factions = [];
        for($x = $chunkX - 3; $x <= $chunkX + 3; $x++) {
            $line = "";
            for($z = $chunkZ - 5; $z <= $chunkZ + 5; $z++) {
                if($x === $chunkX and $z === $chunkZ) {
                    $line .= TextFormat::LIGHT_PURPLE . "+";
                    continue;
                }
                if(($claim = Cryptic::getInstance()->getFactionManager()->getClaimByHash(Level::chunkHash($x, $z))) !== null and $player->getLevel()->getName() === Faction::CLAIM_WORLD) {
                    $line .= TextFormat::DARK_RED . "+";
                    $normalX = $x << 4;
                    $normalZ = $z << 4;
                    $factions["($normalX, $normalZ)"] = $claim->getFaction()->getName();
                    continue;
                }
                $line .= TextFormat::GRAY . "+";
            }
            $lines[] = $line;
        }
        $claim = "None";
        if(($currentClaim = Cryptic::getInstance()->getFactionManager()->getClaimInPosition($player)) !== null) {
            $claim = $currentClaim->getFaction()->getName();
        }
        $lines[] = TextFormat::GREEN . "Current claim: " . TextFormat::WHITE . $claim;
        $lines[] = TextFormat::LIGHT_PURPLE . " + " . TextFormat::DARK_GRAY . "- " . TextFormat::GRAY . "You ({$player->getFloorX()}, {$player->getFloorZ()})";
        $lines[] = TextFormat::GRAY . " + " . TextFormat::DARK_GRAY . "- " . TextFormat::GRAY . "Wilderness";
        foreach($factions as $location => $faction) {
            $lines[] = TextFormat::DARK_RED . " + " . TextFormat::DARK_GRAY . "- " . TextFormat::GRAY . "$faction $location";
        }
        $player->sendForm(new class($lines) extends CustomForm {

            /**
             *  constructor.
             *
             * @param string[] $lines
             */
            public function __construct(array $lines) {
                $elements = [];
                $elements[] = new Label("Map", implode("\n", $lines));
                parent::__construct(TextFormat::DARK_RED. TextFormat::BOLD . "Faction Map", $elements);
            }
        });
    }
}