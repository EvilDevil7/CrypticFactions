<?php

namespace libs\utils;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\Player;

class BossBar {

    /** @var Player */
    private $player;

    /** @var float */
    private $healthPercent = 100;

    /** @var string */
    private $title = "";

    /** @var int */
    private $uniqueId;

    /** @var bool */
    private $spawned = false;

    /**
     * BossBar constructor.
     *
     * @param Player $player
     */
    public function __construct(Player $player) {
        $this->player = $player;
    }

    /**
     * @return bool
     */
    public function isSpawned(): bool {
        return $this->spawned;
    }

    public function spawn() {
        if(!$this->spawned) {
            $this->uniqueId = Entity::$entityCount++;
            $pk = new AddActorPacket();
            $pk->type = Entity::ZOMBIE;
            $pk->entityRuntimeId = $this->uniqueId;
            $pk->position = new Vector3();
            $pk->metadata = [
                Entity::DATA_LEAD_HOLDER_EID => [
                    Entity::DATA_TYPE_LONG,
                    -1
                ],
                Entity::DATA_FLAGS => [
                    Entity::DATA_TYPE_LONG,
                    0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI
                ],
                Entity::DATA_SCALE => [
                    Entity::DATA_TYPE_FLOAT,
                    0
                ],
                Entity::DATA_NAMETAG => [
                    Entity::DATA_TYPE_STRING,
                    $this->title
                ],
                Entity::DATA_BOUNDING_BOX_WIDTH => [
                    Entity::DATA_TYPE_FLOAT,
                    0
                ],
                Entity::DATA_BOUNDING_BOX_HEIGHT => [
                    Entity::DATA_TYPE_FLOAT,
                    0
                ]
            ];
            $this->player->dataPacket($pk);
            $pk = new BossEventPacket();
            $pk->bossEid = $this->uniqueId;
            $pk->eventType = BossEventPacket::TYPE_SHOW;
            $pk->healthPercent = $this->healthPercent / 100;
            $pk->title = $this->title;
            $pk->unknownShort = 0;
            $pk->color = 0;
            $pk->overlay = 0;
            $pk->playerEid = 0;
            $this->player->dataPacket($pk);
            $this->spawned = true;
        }
    }

    /**
     * @param string $title
     * @param float $healthPercent
     */
    public function update(string $title, float $healthPercent) {
        if($this->isSpawned()) {
            $this->despawn();
        }
        $this->title = $title;
        if($healthPercent > 100) {
            $healthPercent = 100;
        }
        $this->healthPercent = $healthPercent;
        $this->spawn();
    }

    public function despawn() {
        $pk = new RemoveActorPacket();
        $pk->entityUniqueId = $this->uniqueId;
        $this->player->dataPacket($pk);
        $this->spawned = false;
    }
}
