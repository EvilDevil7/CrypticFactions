<?php

declare(strict_types = 1);

namespace libs\utils;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class FloatingTextParticle extends \pocketmine\level\particle\FloatingTextParticle {

    /** @var string */
    private $identifier;

    /** @var string */
    private $message;

    /** @var Level */
    private $level;

    /**
     * FloatingTextParticle constructor.
     *
     * @param Position $pos
     * @param string $identifier
     * @param string $message
     */
    public function __construct(Position $pos, string $identifier, string $message) {
        parent::__construct($pos, "", "");
        $this->level = $pos->getLevel();
        $this->identifier = $identifier;
        $this->message = $message;
        $this->update();
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return Level
     */
    public function getLevel(): Level {
        return $this->level;
    }

    /**
     * @param null|string $message
     */
    public function update(?string $message = null): void {
        $this->message = $message ?? $this->message;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function sendChangesToAll(): void {
        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
            $this->sendChangesTo($player);
        }
    }

    /**
     * @param Position $position
     */
    public function move(Position $position) {
        $this->setComponents($position->getX(), $position->getY(), $position->getZ());
    }

    /**
     * @param Player $player
     */
    public function sendChangesTo(Player $player): void {
        $this->setTitle($this->message);
        $level = $player->getLevel();
        if($level === null) {
            return;
        }
        if($this->level->getName() !== $level->getName()) {
            return;
        }
        $this->level->addParticle($this, [$player]);
    }

    /**
     * @param Player $player
     */
    public function spawn(Player $player): void {
        $this->setInvisible(false);
        $level = $player->getLevel();
        if($level === null) {
            return;
        }
        $this->level->addParticle($this, [$player]);
    }

    /**
     * @param Player $player
     */
    public function despawn(Player $player): void {
        $this->setInvisible(true);
        $level = $player->getLevel();
        if($level === null) {
            return;
        }
        $this->level->addParticle($this, [$player]);
    }
}