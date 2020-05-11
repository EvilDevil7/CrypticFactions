<?php

declare(strict_types=1);

namespace core\rank;

use core\Cryptic;
use core\CrypticPlayer;
use core\discord\DiscordManager;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;

class RankListener implements Listener{

	/** @var Cryptic */
	private $core;

	/**
	 * GroupListener constructor.
	 *
	 * @param Cryptic $core
	 */
	public function __construct(Cryptic $core){
		$this->core = $core;
	}

	/**
	 * @priority HIGHEST
	 * @param PlayerChatEvent $event
	 */
	public function onPlayerChat(PlayerChatEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getPlayer();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$mode = $player->getChatMode();

		$webhook = null;
		if($mode === CrypticPlayer::PUBLIC){
			$webhook = "690662149349179392/Wo7A4er0HqIQ2cMkYarLk_nZtJ5d4fTEiaeGoxnM1p7_kDKgpKyP8HojDooQEgIaASXe";
		}elseif($mode === CrypticPlayer::STAFF){
			$webhook = "690663498535141376/czGtdiWpZS710RytoevQOM8Ewyo7Hwr5ezIBERatMCa5q61hOq1dRFJbtUORS70g9PNI";
		}
		if($webhook !== null)
			DiscordManager::postWebhook($webhook, $event->getMessage(), $player->getName() . " (" . $player->getRank()->getName() . ")");

		$faction = $player->getFaction();
		if($faction === null and ($mode === CrypticPlayer::FACTION or $mode === CrypticPlayer::ALLY)){
			$mode = CrypticPlayer::PUBLIC;
			$player->setChatMode($mode);
		}
		if($mode === CrypticPlayer::PUBLIC){
			$event->setFormat($player->getRank()->getChatFormatFor($player, $event->getMessage(), [
				"faction_rank" => $player->getFactionRoleToString(),
				"faction" => ($faction = $player->getFaction()) !== null ? $faction->getName() : "",
				"kills" => $player->getKills()
			]));
			return;
		}
		$event->setCancelled();
		if($mode === CrypticPlayer::STAFF){
			/** @var CrypticPlayer $staff */
			foreach($this->core->getServer()->getOnlinePlayers() as $staff){
				$rank = $staff->getRank();
				if($rank->getIdentifier() >= Rank::TRAINEE and $rank->getIdentifier() <= Rank::OWNER){
					$staff->sendMessage(TextFormat::DARK_GRAY . "[" . $player->getRank()->getColoredName() . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . $player->getName() . TextFormat::GRAY . ": " . $event->getMessage());
				}
				if($rank->getIdentifier() === Rank::DEVELOPER){
					$staff->sendMessage(TextFormat::DARK_GRAY . "[" . $player->getRank()->getColoredName() . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . $player->getName() . TextFormat::GRAY . ": " . $event->getMessage());
				}
				if($rank->getIdentifier() === Rank::BUILDER){
				$staff->sendMessage(TextFormat::DARK_GRAY . "[" . $player->getRank()->getColoredName() . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . $player->getName() . TextFormat::GRAY . ": " . $event->getMessage());
				}
			}
			return;
		}
		if($player->getChatMode() === CrypticPlayer::FACTION){
			$onlinePlayers = $faction->getOnlineMembers();
			foreach($onlinePlayers as $onlinePlayer){
				$onlinePlayer->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::BOLD . TextFormat::RED . "FC" . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . $player->getName() . TextFormat::GRAY . ": " . $event->getMessage());
			}
		}else{
			$allies = $faction->getAllies();
			$onlinePlayers = $faction->getOnlineMembers();
			foreach($allies as $ally){
				if(($ally = $this->core->getFactionManager()->getFaction($ally)) === null){
					continue;
				}
				$onlinePlayers = array_merge($ally->getOnlineMembers(), $onlinePlayers);
			}
			foreach($onlinePlayers as $onlinePlayer){
				$onlinePlayer->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::BOLD . TextFormat::GOLD . "AC" . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . $player->getName() . TextFormat::GRAY . ": " . $event->getMessage());
			}
		}
	}

	/**
	 * @priority NORMAL
	 * @param EntityRegainHealthEvent $event
	 */
	public function onEntityRegainHealth(EntityRegainHealthEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getEntity();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$player->setScoreTag(TextFormat::WHITE . round($player->getHealth(), 1) . TextFormat::RED . TextFormat::BOLD . " HP");
	}

	/**
	 * @priority NORMAL
	 * @param EntityDamageEvent $event
	 */
	public function onEntityDamage(EntityDamageEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getEntity();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$player->setScoreTag(TextFormat::WHITE . round($player->getHealth(), 1) . TextFormat::RED . TextFormat::BOLD . " HP");
	}
}
