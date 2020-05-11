<?php

declare(strict_types = 1);

namespace core\quest;

abstract class Quest {

    const DAMAGE = 0;

    const BREAK = 1;

    const KILL = 2;

    const PLACE = 3;

    const BUY = 4;

    const SELL = 5;

    const CLAIM_ENVOY = 6;

    const EASY = 1;

    const MEDIUM = 2;

    const HARD = 3;

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var int */
    protected $eventType;

    /** @var callable */
    protected $callable;

    /** @var int */
    protected $targetValue;

    /** @var int */
    protected $difficulty;

    /**
     * Quest constructor.
     *
     * @param string $name
     * @param string $description
     * @param int $eventType
     * @param int $targetValue
     * @param int $difficulty
     * @param callable $callable
     */
    public function __construct(string $name, string $description, int $eventType, int $targetValue, int $difficulty, callable $callable) {
        $this->name = $name;
        $this->description = $description;
        $this->eventType = $eventType;
        $this->targetValue = $targetValue;
        $this->difficulty = $difficulty;
        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getEventType(): int {
        return $this->eventType;
    }

    /**
     * @return int
     */
    public function getTargetValue(): int {
        return $this->targetValue;
    }

    /**
     * @return int
     */
    public function getDifficulty(): int {
        return $this->difficulty;
    }

    /**
     * @return string
     */
    public function getDifficultyName(): string {
        switch($this->difficulty) {
            case self::EASY:
                return "Easy";
                break;
            case self::MEDIUM:
                return "Medium";
                break;
            case self::HARD:
                return "Hard";
                break;
            default:
                return "Unknown";
        }
    }

    /**
     * @return callable
     */
    public function getCallable(): callable {
        return $this->callable;
    }
}