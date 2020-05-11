<?php

declare(strict_types = 1);

namespace core\price;

class ShopPlace {

    /** @var string */
    private $name;

    /** @var PriceEntry[] */
    private $entries = [];

    /**
     * ShopPlace constructor.
     *
     * @param string $name
     * @param PriceEntry[] $entries
     */
    public function __construct(string $name, array $entries) {
        $this->name = $name;
        foreach($entries as $entry) {
            $this->entries[$entry->getName()] = $entry;
        }
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return PriceEntry[]
     */
    public function getEntries(): array {
        return $this->entries;
    }

    /**
     * @param string $name
     *
     * @return PriceEntry|null
     */
    public function getEntry(string $name): ?PriceEntry {
        return $this->entries[$name] ?? null;
    }
}