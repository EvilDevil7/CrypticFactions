<?php

declare(strict_types = 1);

namespace core\envoy;

use core\envoy\event\EnvoyClaimEvent;
use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\UtilsException;
use pocketmine\block\Block;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Server;
use pocketmine\tile\Tile;
use pocketmine\tile\Chest;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

class Envoy {

    /** @var Position */
    private $position;

    /** @var int */
    private $time;

    /** @var bool */
    private $spawned = false;

    /**
     * Envoy constructor.
     *
     * @param Position $position
     */
    public function __construct(Position $position) {
        $this->position = $position;
        $this->time = time();
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return int
     */
    public function getTimeLeft(): int {
        return 600 - (time() - $this->time);
    }

    /**
     * @throws UtilsException
     */
    public function tick(): void {
        if((600 - (time() - $this->time)) <= 0) {
            $this->despawn();
            return;
        }
        if($this->spawned === false) {
            $this->spawn();
        }
        else {
            $this->update();
        }
    }

    public function spawn(): void {
        $chest = Tile::createTile("Chest", $this->position->level, Chest::createNBT($this->position));
        $this->position->getLevel()->setBlock(new Vector3($chest->getX(), $chest->getY(), $chest->getZ()), Block::get(Block::CHEST), true, true);
        $this->setSpawned(true);
        Cryptic::getInstance()->getEnvoyManager()->spawnEnvoy($this->position);
    }

    /**
     * @return bool
     */
    public function isSpawned(): bool {
        return $this->spawned;
    }

    /**
     * @param bool $value
     */
    public function setSpawned(bool $value): void {
        $this->spawned = $value;
    }

    /**
     * @param CrypticPlayer $player
     *
     */
    public function claim(CrypticPlayer $player): void {
        $rewards = Cryptic::getInstance()->getEnvoyManager()->getRewards();
        $event = new EnvoyClaimEvent($player, $rewards);
        $event->call();
        foreach($event->getItems() as $item) {
            $this->position->getLevel()->dropItem($this->position, $item->getItem());
        }
        $this->position->getLevel()->addParticle(new HugeExplodeParticle($this->position));
        $this->position->getLevel()->broadcastLevelSoundEvent($this->position, LevelSoundEventPacket::SOUND_EXPLODE);
    }

    /**
     * @throws UtilsException
     */
    public function update(): void {
        $players = $this->position->getLevel()->getViewersForPosition($this->position);
        $time = 600 - (time() - $this->time);
        if($time <= 0) {
            $this->despawn();
            return;
        }
        $chunk = $this->position->getLevel()->getChunkAtPosition($this->position);
        if($chunk === null) {
            return;
        }
        $block = $this->position->getLevel()->getBlock($this->position);
        if($block->getId() !== Block::CHEST) {
            $this->position->getLevel()->setBlock($this->position, Block::get(Block::CHEST));
        }
        $message = "§l§b» ENVOY «§r\n§7The envoy is disappearing in §b" . gmdate("i:s", $time) . "§7...";
        foreach($players as $player) {
            if(!$player instanceof CrypticPlayer) {
                continue;
            }
            $text = $player->getFloatingText("Envoy$this->position");
            if($text === null) {
                $player->addFloatingText(Position::fromObject($this->position->add(0.5, 1.25, 0.5), $this->position->getLevel()), "Envoy$this->position", $message);
                continue;
            }
            $text->update($message);
            $text->sendChangesTo($player);
        }
    }

    /**
     * @throws UtilsException
     */
    public function despawn(): void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            if(!$player instanceof CrypticPlayer) {
                continue;
            }
            $text = $player->getFloatingText("Envoy$this->position");
            if($text === null) {
                continue;
            }
            $player->removeFloatingText("Envoy$this->position");
        }
        $this->position->getLevel()->setBlock($this->position, Block::get(Block::AIR));
        $this->setSpawned(false);
        Cryptic::getInstance()->getEnvoyManager()->despawnEnvoy($this->position);
    }
}
