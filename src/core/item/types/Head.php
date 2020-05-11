<?php

declare(strict_types=1);

namespace core\item\types;

use core\item\CustomItem;
use core\CrypticPlayer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class Head extends CustomItem{

	const PLAYER = "Player";

	/**
	 * Head constructor.
	 *
	 * @param CrypticPlayer $player
	 */
	public function __construct(CrypticPlayer $player){
		$customName = "§b{$player->getName()}'s Head§r";
		$lore = [];
		$lore[] = "";
		$lore[] = "§7You will receive §b10%§r §7of §b{$player->getName()}§7’s money balance.§r";
		$lore[] = "";
		$lore[] = TextFormat::RESET . TextFormat::YELLOW . "Tap anywhere to claim.";
		$this->setNamedTagEntry(new CompoundTag(self::CUSTOM));
		/** @var CompoundTag $tag */
		$tag = $this->getNamedTagEntry(self::CUSTOM);
		$tag->setString(self::PLAYER, $player->getXuid());
		$tag->setString("UniqueId", uniqid());
		$tag->setInt("Balance", intval($player->getBalance() * 0.1));
		$tag->setString("Name", $player->getName());
		$player->subtractFromBalance(intval(($player->getBalance() * 0.1)));
		parent::__construct(self::MOB_HEAD, $customName, $lore, [], [], 3);
	}
}