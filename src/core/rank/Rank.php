<?php

declare(strict_types = 1);

namespace core\rank;

use core\Cryptic;
use core\CrypticPlayer;

class Rank {

    const PLAYER = 0;

    const KNIGHT = 1;

    const WIZARD = 2;

    const KING = 3;

    const MYSTIC = 4;

    const CRYPTIC = 5;

    const GOD = 6;

    const WARLORD = 7;

    const TRAINEE = 8;

    const MODERATOR = 9;

    const SENIOR_MODERATOR = 10;

    const ADMIN = 11;

    const SENIOR_ADMIN = 12;

    const MANAGER = 13;

    const OWNER = 14;

    const YOUTUBER = 15;

    const OVERLORD = 16;

    const FAMOUS = 17;
    
    const DEVELOPER = 18;
    
    const BUILDER = 19;

    /** @var string */
    private $name;

    /** @var string */
    private $coloredName;

    /** @var int */
    private $identifier;

    /** @var string */
    private $chatFormat;

    /** @var string */
    private $tagFormat;

    /** @var array */
    private $permissions = [];

    /** @var int */
    private $homes;

    /** @var string */
    private $chatColor;

    /**
     * Rank constructor.
     *
     * @param string $name
     * @param string $chatColor
     * @param string $coloredName
     * @param int $identifier
     * @param string $chatFormat
     * @param string $tagFormat
     * @param int $homes
     * @param int $vaults
     * @param array $permissions
     */
    public function __construct(string $name, string $chatColor, string $coloredName, int $identifier, string $chatFormat, string $tagFormat, int $homes, int $vaults, array $permissions = []) {
        $this->name = $name;
        $this->chatColor = $chatColor;
        $this->coloredName = $coloredName;
        $this->identifier = $identifier;
        $this->chatFormat = $chatFormat;
        $this->tagFormat = $tagFormat;
        $this->homes = $homes;
        for($i = 1; $i <= $vaults; $i++) {
            $permissions = array_merge($permissions, ["playervaults.vault.$i"]);
        }
        $this->permissions = $permissions;
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
    public function getColoredName(): string {
        return $this->coloredName;
    }

    /**
     * @return string
     */
    public function getChatColor(): string {
        return $this->chatColor;
    }

    /**
     * @return int
     */
    public function getIdentifier(): int {
        return $this->identifier;
    }

    /**
     * @param CrypticPlayer $player
     * @param string        $message
     * @param array         $args
     *
     * @return string
     */
    public function getChatFormatFor(CrypticPlayer $player, string $message, array $args = []): string {
        $man = Cryptic::getInstance()->getTagManager();
        $tag = $man->getTag($player, true) . " ";
        if($tag == null){
            $tag = "";
        }
        $format = $this->chatFormat;
        foreach($args as $arg => $value) {
            $format = str_replace("{" . $arg . "}", $value, $format);
        }
        $format = str_replace("{player}", $player->getDisplayName(), $format);
        $format = str_replace("{tag}", $tag, $format);
        return str_replace("{message}", $message, $format);
    }

    /**
     * @param CrypticPlayer $player
     * @param array         $args
     *
     * @return string
     */
    public function getTagFormatFor(CrypticPlayer $player, array $args = []): string {
        $man = Cryptic::getInstance()->getTagManager();
        $tag = $man->getTag($player, true) . " ";
        if($tag == null){
            $tag = "";
        }
        $format = $this->tagFormat;
        foreach($args as $arg => $value) {
            $format = str_replace("{" . $arg . "}", $value, $format);
        }
        $format = str_replace("{tag}", $tag, $format);
        return str_replace("{player}", $player->getDisplayName(), $format);
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getHomeLimit(): int {
        return $this->homes;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->name;
    }
}
