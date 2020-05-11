<?php

declare(strict_types = 1);

namespace core\crate\types;

use core\crate\Crate;
use core\crate\Reward;
use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\UtilsException;
use pocketmine\level\Position;

class TagCrate extends Crate {

    /**
     * TagCrate constructor.
     *
     * @param Position $position
     */
    public function __construct(Position $position) {
        $tag = Cryptic::getInstance()->getTagManager();
        parent::__construct(self::TAG, $position, [
        new Reward("Cryptic4Lyfe Tag", $tag->getTagNote("Cryptic4Lyfe"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Cryptic4Lyfe"));
            }, 45),
        new Reward("Godly Tag", $tag->getTagNote("Godly"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Godly"));
            }, 45),
        new Reward("OG Tag", $tag->getTagNote("OG"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("OG"));
            }, 45),
        new Reward("Toxic Tag", $tag->getTagNote("Toxic"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Toxic"));
            }, 45),
        new Reward("Rusty Tag", $tag->getTagNote("Rusty"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Rusty"));
            }, 45),
        new Reward("Sweat Tag", $tag->getTagNote("Sweat"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Sweat"));
            }, 45),
        new Reward("Tryhard Tag", $tag->getTagNote("Tryhard"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Tryhard"));
            }, 45),
        new Reward("EGIRL Tag", $tag->getTagNote("EGIRL"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("EGIRL"));
            }, 45),
        new Reward("Bot Tag", $tag->getTagNote("Bot"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Bot"));
            }, 45),
        new Reward("Bot Tag", $tag->getTagNote("Bot"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Bot"));
            }, 45),
        new Reward("Minemen Tag", $tag->getTagNote("Minemen"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Minemen"));
            }, 45),
        new Reward("EZPZ Tag", $tag->getTagNote("EZPZ"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("EZPZ"));
            }, 45),
        new Reward("HypeBeast Tag", $tag->getTagNote("HypeBeast"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("HypeBeast"));
            }, 45),
        new Reward("Clickbait Tag", $tag->getTagNote("Clickbait"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("Clickbait"));
            }, 45),
        new Reward("EpicGames Tag", $tag->getTagNote("EpicGames"), function(CrypticPlayer $player)use($tag): void {
                $player->getInventory()->addItem($tag->getTagNote("EpicGames"));
            }, 45),

        ]);
    }

    /**
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function spawnTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle !== null) {
            return;
        }
        $player->addFloatingText(Position::fromObject($this->getPosition()->add(0.5, 1.25, 0.5), $this->getPosition()->getLevel()), $this->getName(), "§l§dTags Crate§r\n§7You have §d" . $player->getSession()->getKeys($this) . " §7keys!§r");
    }

    /**
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function updateTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle === null) {
            $this->spawnTo($player);
        }
        $text = $player->getFloatingText($this->getName());
        $text->update("§l§dTags Crate§r\n§7You have §d" . $player->getSession()->getKeys($this) . " §7keys!§r");
        $text->sendChangesTo($player);
    }

    /**
     * @param CrypticPlayer $player
     */
    public function despawnTo(CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle !== null) {
            $particle->despawn($player);
        }
    }

    /**
     * @param Reward        $reward
     * @param CrypticPlayer $player
     *
     * @throws UtilsException
     */
    public function showReward(Reward $reward, CrypticPlayer $player): void {
        $particle = $player->getFloatingText($this->getName());
        if($particle === null) {
            $this->spawnTo($player);
        }
        $text = $player->getFloatingText($this->getName());
        $text->update("§l§d" . $reward->getName());
        $text->sendChangesTo($player);
    }
}
