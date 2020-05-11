<?php

declare(strict_types = 1);

namespace core\crate\task;

use core\crate\Crate;
use core\crate\Reward;
use core\Cryptic;
use core\CrypticPlayer;
use pocketmine\entity\Entity;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddItemActorPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\scheduler\Task;

class AnimationTask extends Task {

    /** @var int */
    private $runs = 0;

    /** @var Crate */
    private $crate;

    /** @var CrypticPlayer */
    private $player;

    /** @var int */
    private $id;

    /**
     * AnimationTask constructor.
     *
     * @param Crate         $crate
     * @param CrypticPlayer $player
     */
    public function __construct(Crate $crate, CrypticPlayer $player) {
        $this->crate = $crate;
        $player->setRunningCrateAnimation();
        $this->player = $player;
    }

    /**
     * @param Reward $reward
     */
    public function spawnItemEntity(Reward $reward) {
        $this->id = Entity::$entityCount++;
        $pk = new AddItemActorPacket();
        $pk->item = $reward->getItem();
        $pk->position = $this->crate->getPosition()->add(0.5, 0.75, 0.5);
        $pk->entityRuntimeId = $this->id;
        $this->player->dataPacket($pk);
    }

    public function removeItemEntity() {
        $pk = new RemoveActorPacket();
        $pk->entityUniqueId = $this->id;
        $this->player->dataPacket($pk);
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        if($this->player->isClosed()) {
            Cryptic::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }
        ++$this->runs;
        $position = $this->crate->getPosition();
        if($this->runs === 1) {
            $pk = new LevelSoundEventPacket();
            $pk->position = $position;
            $pk->sound = LevelSoundEventPacket::SOUND_CHEST_OPEN;
            $this->player->sendDataPacket($pk);
            $pk = new BlockEventPacket();
            $pk->x = $position->getFloorX();
            $pk->y = $position->getFloorY();
            $pk->z = $position->getFloorZ();
            $pk->eventType = 1;
            $pk->eventData = 1;
            $this->player->sendDataPacket($pk);
            return;
        }
        if($this->runs === 2) {
            $pk = new LevelSoundEventPacket();
            $pk->position = $position;
            $pk->sound = LevelSoundEventPacket::SOUND_LAUNCH;
            $this->player->sendDataPacket($pk);
        }
        if($this->runs === 4) {
            $cx = $position->getX() + 0.5;
            $cy = $position->getY() + 1.2;
            $cz = $position->getZ() + 0.5;
            $radius = 1;
            for($i = 0; $i < 21; $i += 1.1){
                $x = $cx + ($radius * cos($i));
                $z = $cz + ($radius * sin($i));
                $pos = new Vector3($x, $cy, $z);
                $position->level->addParticle(new FlameParticle($pos), [$this->player]);
            }
            $reward = $this->crate->getReward();
            $callable = $reward->getCallback();
            $callable($this->player);
            $pk = new LevelSoundEventPacket();
            $pk->position = $position;
            $pk->sound = LevelSoundEventPacket::SOUND_BLAST;
            $this->player->sendDataPacket($pk);
            $this->spawnItemEntity($reward);
            $this->crate->showReward($reward, $this->player);
            return;
        }
        if($this->runs === 7) {
            $pk = new LevelSoundEventPacket();
            $pk->position = $position;
            $pk->sound = LevelSoundEventPacket::SOUND_CHEST_CLOSED;
            $this->player->sendDataPacket($pk);
            $pk = new BlockEventPacket();
            $pk->x = $position->getFloorX();
            $pk->y = $position->getFloorY();
            $pk->z = $position->getFloorZ();
            $pk->eventType = 1;
            $pk->eventData = 0;
            $this->player->sendDataPacket($pk);
            $this->removeItemEntity();
            $this->crate->updateTo($this->player);
            $this->player->setRunningCrateAnimation(false);
            Cryptic::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        }
    }
}
