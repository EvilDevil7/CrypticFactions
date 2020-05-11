<?php

declare(strict_types = 1);

namespace core\kit;

use core\CrypticPlayer;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class Kit {

    const COMMON = 1;

    const UNCOMMON = 2;

    const RARE = 3;

    const EPIC = 4;

    const LEGENDARY = 5;

    const MYTHIC = 6;

	/** @var string */
	private $name;

	/** @var Item[] */
	private $items = [];

    /** @var int */
	private $rarity;

	/** @var int */
	private $cooldown;

    /**
     * Kit constructor.
     *
     * @param string $name
     * @param int $rarity
     * @param array $items
     * @param int $cooldown
     */
	public function __construct(string $name, int $rarity, array $items, int $cooldown) {
		$this->name = $name;
		$this->rarity = $rarity;
		$this->items = $items;
		$this->cooldown = $cooldown;
	}

    /**
     * @param CrypticPlayer $player
     * @return bool
     */
	public function isInvFull(CrypticPlayer $player): bool{
	    $inv = $player->getInventory();
	    for($i = 0; $i < $inv->getSize(); $i++){
	        if($inv->getItem($i)->getId() === 0){
	            return false;
            }
        }
	    return true;
    }

//    /**
//     * @param CrypticPlayer $player
//     * @param Item $item
//     */
//    public function giveItem(CrypticPlayer $player, Item $item): void{
//	    if($this->isInvFull($player)){
//	        $player->getLevel()->dropItem($player->add(0, 0.5, 0), $item);
//        }else{
//	        $player->getInventory()->addItem($item);
//        }
//    }

    /**
     * @param CrypticPlayer $player
     */
    public function giveTo(CrypticPlayer $player): bool {
        if($this->isInvFull($player)){
            $player->sendMessage(TextFormat::RED . "Your inventory is full.");
            return false;
        }
        foreach($this->items as $item) {
            if($item->getId() !== Item::AIR){
                $player->getInventory()->addItem($item);
            }
        }
        return true;
    }


    /**
     * @return string
     */
	public function getName(): string {
		return $this->name;
	}

    /**
     * @return int
     */
	public function getRarity(): int {
		return $this->rarity;
	}

	/**
	 * @return Item[]
	 */
	public function getItems(): array {
		return $this->items;
	}

    /**
     * @return int
     */
	public function getCooldown(): int {
	    return $this->cooldown;
    }

    /**
     * @param int $rarity
     *
     * @return string
     */
    public static function rarityToString(int $rarity): string {
	    switch($rarity) {
            case self::COMMON:
                return "Common";
                break;
            case self::UNCOMMON:
                return "Uncommon";
                break;
            case self::RARE:
                return "Rare";
                break;
            case self::EPIC:
                return "Epic";
                break;
            case self::LEGENDARY:
                return "Legendary";
                break;
            case self::MYTHIC:
                return "Mythic";
                break;
            default:
                return "Unknown";
                break;
        }
    }
}
