<?php

declare(strict_types = 1);

namespace core\crate;

use core\crate\task\AnimationTask;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\level\Position;

abstract class Crate {

    const COMMON = "Common";

    const LEGENDARY = "Legendary";

    const EPIC = "Epic";
 
    const RARE = "Rare";

    const VOTE = "Vote";

    const TAG = "Tag";

    /** @var string */
    private $name;

    /** @var Position */
    private $position;

    /** @var Reward[] */
    private $rewards = [];

    /**
     * Crate constructor.
     *
     * @param string $name
     * @param Position $position
     * @param Reward[] $rewards
     */
    public function __construct(string $name, Position $position, array $rewards) {
        $this->name = $name;
        $this->position = $position;
        $this->rewards = $rewards;
    }

    /**
     * @param CrypticPlayer $player
     */
    abstract public function spawnTo(CrypticPlayer $player): void;

    /**
     * @param CrypticPlayer $player
     */
    abstract public function updateTo(CrypticPlayer $player): void;

    /**
     * @param CrypticPlayer $player
     */
    abstract public function despawnTo(CrypticPlayer $player): void;

    /**
     * @param Reward        $reward
     * @param CrypticPlayer $player
     */
    abstract public function showReward(Reward $reward, CrypticPlayer $player): void;

    /**
     * @param CrypticPlayer $player
     *
     * @throws TranslationException
     */
    public function try(CrypticPlayer $player): void {
        if($player->isRunningCrateAnimation() === true) {
            $player->sendMessage(Translation::getMessage("animationAlreadyRunning"));
            $player->knockBack($player, 0, $player->getX() - $this->position->getX(), $player->getZ() - $this->position->getZ(), 1);
            return;
        }
        if($player->getInventory()->getSize() === count($player->getInventory()->getContents())) {
            $player->sendMessage(Translation::getMessage("fullInventory"));
            $player->knockBack($player, 0, $player->getX() - $this->position->getX(), $player->getZ() - $this->position->getZ(), 1);
            return;
        }
        if($player->getKeys($this) <= 0) {
            $player->sendMessage(Translation::getMessage("noKeys"));
            $player->knockBack($player, 0, $player->getX() - $this->position->getX(), $player->getZ() - $this->position->getZ(), 1);
            return;
        }
        $player->removeKeys($this);
        Cryptic::getInstance()->getScheduler()->scheduleRepeatingTask(new AnimationTask($this, $player), 10);
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return Position
     */
    public function getPosition(): Position {
        return $this->position;
    }

    /**
     * @return Reward[]
     */
    public function getRewards(): array {
        return $this->rewards;
    }

    /**
     * @param int $loop
     *
     * @return Reward
     */
    public function getReward(int $loop = 0): Reward {
        $chance = mt_rand(0, 100);
        $reward = $this->rewards[array_rand($this->rewards)];
        if($loop >= 10) {
            return $reward;
        }
        if($reward->getChance() <= $chance) {
            return $this->getReward($loop + 1);
        }
        return $reward;
    }
}
