<?php

declare(strict_types = 1);

namespace core\item\enchantment;

abstract class Enchantment extends \pocketmine\item\enchantment\Enchantment implements EnchantmentIdentifiers {

    const DAMAGE = 0;

    const BREAK = 1;

    const EFFECT_ADD = 2;

    const MOVE = 3;

    const DEATH = 4;

    const SHOOT = 5;

    const INTERACT = 6;

    const DAMAGE_BY = 7;

    /** @var callable */
    protected $callable;

    /** @var string */
    private $description;

    /** @var int */
    private $eventType;

    /**
     * Enchantment constructor.
     *
     * @param int $id
     * @param string $name
     * @param int $rarity
     * @param string $description
     * @param int $eventType
     * @param int $flag
     * @param int $maxLevel
     */
    public function __construct(int $id, string $name, int $rarity, string $description, int $eventType, int $flag, int $maxLevel) {
        $this->description = $description;
        $this->eventType = $eventType;
        parent::__construct($id, $name, $rarity, $flag, self::SLOT_NONE, $maxLevel);
    }

    /**
     * @return int
     */
    public function getEventType(): int {
        return $this->eventType;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @return callable
     */
    public function getCallable(): callable {
        return $this->callable;
    }
}
