<?php

declare(strict_types=1);

namespace core\item;

use core\event\EventManager;
use core\item\enchantment\Enchantment;
use core\item\task\HolyBoxAnimationTask;
use core\item\types\BossEgg;
use core\item\types\ChestKit;
use core\item\types\CrateKeyNote;
use core\item\types\Drops;
use core\item\types\Head;
use core\item\types\HolyBox;
use core\item\types\MoneyNote;
use core\item\types\Present;
use core\item\types\SacredStone;
use core\item\types\SellWand;
use core\item\types\XPNote;
use core\Cryptic;
use core\CrypticPlayer;
use core\price\event\ItemSellEvent;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\utils\UtilsException;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilBreakSound;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\scheduler\Task;
use pocketmine\tile\Container;
use pocketmine\utils\TextFormat;

class ItemListener implements Listener{

	/** @var Cryptic */
	private $core;

	/** @var array */
	private $ids = [
		Block::COAL_ORE,
		Block::DIAMOND_ORE,
		Block::EMERALD_ORE,
		Block::REDSTONE_ORE,
		Block::LAPIS_ORE,
		Block::NETHER_QUARTZ_ORE
	];

	/** @var int */
	private $sellWandCooldown = [];

	/**
	 * ItemListener constructor.
	 *
	 * @param Cryptic $core
	 */
	public function __construct(Cryptic $core){
		$this->core = $core;
	}

	/**
	 * @priority NORMAL
	 * @param PlayerJoinEvent $event
	 */
	public function onPlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		if($player->getArmorInventory() !== null)
			$player->setActiveArmorEnchantments();
	}

	/**
	 * @priority LOWEST
	 * @param PlayerChatEvent $event
	 */
	public function onPlayerChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$item = $player->getInventory()->getItemInHand();
		$name = TextFormat::RESET . TextFormat::WHITE . $item->getName();
		if($item->hasCustomName()){
			$name = $item->getCustomName();
		}
		$replace = TextFormat::DARK_GRAY . "[" . $name . TextFormat::RESET . TextFormat::GRAY . " * " . TextFormat::WHITE . $item->getCount() . TextFormat::DARK_GRAY . "]" . TextFormat::RESET . $player->getRank()->getChatColor();
		$message = $event->getMessage();
		$message = str_replace("[item]", $replace, $message);
		$event->setMessage($message);
	}

	/**
	 * @priority NORMAL
	 * @param PlayerItemHeldEvent $event
	 */
	public function onPlayerItemHeld(PlayerItemHeldEvent $event){
		$player = $event->getPlayer();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$item = $event->getItem();
		if($item->hasEnchantments()){
			if($item->hasEnchantment(Enchantment::KNOCKBACK)){
				$player->getInventory()->removeItem($item);
				$item->removeEnchantment(Enchantment::KNOCKBACK);
				$player->getInventory()->addItem($item);
			}
		}
		if($item->getId() === Item::BUCKET){
			$player->getInventory()->removeItem($item);
			return;
		}
		$player->setActiveHeldItemEnchantments();
	}

	/**
	 * @priority HIGHEST
	 * @param PlayerInteractEvent $event
	 *
	 * @throws TranslationException
	 * @throws UtilsException
	 */
	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		$item = $event->getItem();
		$player = $event->getPlayer();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$inventory = $player->getInventory();
		if($item->getId() === Item::EXPERIENCE_BOTTLE){
			$xp = 0;
			for($i = 0; $i <= $item->getCount(); ++$i){
				$xp += mt_rand(6, 18);
			}
			$player->addXp($xp);
			$inventory->removeItem($item);
			$event->setCancelled();
			return;
		}
		$tag = $item->getNamedTagEntry(CustomItem::CUSTOM);
		if($tag === null){
			return;
		}
		if($tag instanceof CompoundTag){
			if($tag->hasTag(CrateKeyNote::CRATE, StringTag::class) and $tag->hasTag(CrateKeyNote::AMOUNT, IntTag::class)){
				$crate = $tag->getString(CrateKeyNote::CRATE);
				$amount = $tag->getInt(CrateKeyNote::AMOUNT);
				$crate = $this->core->getCrateManager()->getCrate($crate);
				$player->getSession()->addKeys($crate, $amount);
				$player->sendMessage("§l§8(§a!§8)§r §7You've successfully claimed your §a" . $amount . " §2" . $crate->getName() . " §7crate key(s)!§r");
				$player->playXpLevelUpSound();
				$player->getLevel()->addSound(new BlazeShootSound($player));
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$event->setCancelled();
			}
			if($tag->hasTag(ChestKit::KIT, StringTag::class)){
				$kit = $tag->getString(ChestKit::KIT);
				$kit = $this->core->getKitManager()->getKitByName($kit);
				if($kit->giveTo($player)){
					$player->addTitle("§l§8[§6+§8]§r §7Equipped Kit§r §l§8[§6+§8]§r", "§l§6" . $kit->getName() . " Kit§r");
					$player->sendMessage("§l§8[§6+§8]§r §7You've sucessfully §6redeemed §7your §l§6" . $kit->getName() . "§r §7kit!§r");
					$player->getLevel()->addSound(new AnvilBreakSound($player));
					$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				}
				$event->setCancelled();
			}
			if($tag->hasTag(XPNote::XP, IntTag::class)){
				$amount = $tag->getInt(XPNote::XP);
				$player->sendMessage("§l§8(§a!§8)§r §7You've successfully added §a" . $amount . " §7to your XP bar!§r");
				$player->playXpLevelUpSound();
				$player->addXp($amount);
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$event->setCancelled();
			}
			if($tag->hasTag(MoneyNote::BALANCE, IntTag::class)){
				$amount = $tag->getInt(MoneyNote::BALANCE);
				$player->sendMessage("§l§8(§a!§8)§r §7You've successfully added §a$" . $amount . " §7to your balance!§r");
				$player->getLevel()->addSound(new BlazeShootSound($player));
				$player->addToBalance($amount);
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$event->setCancelled();
			}
			if($tag->hasTag(SacredStone::SACRED_STONE, StringTag::class)){
				if(mt_rand(1, 5) == 1){
					$kits = $this->core->getKitManager()->getSacredKits();
					$kit = $kits[array_rand($kits)];
					$player->getLevel()->addSound(new BlazeShootSound($player));
					$player->getInventory()->addItem((new HolyBox($kit))->getItemForm());
				}else{
					$player->getLevel()->addSound(new AnvilUseSound($player));
					$player->addToBalance(mt_rand(1000, 5000));
					$player->sendMessage("§l§8(§c!§8)§r §7You got unlucky with this sacred stone.\n§l§8(§a!§8)§r §7Since you got unlucky with your sacred stone, you've been granted $1,000-$5,000 randomly. Figure out how much you've made!§r");
				}
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$event->setCancelled();
			}
			if($tag->hasTag(Present::PRESENT, StringTag::class)){
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$reward = EventManager::getGiftChooser()->getReward();
				$player->addTitle("§cYou've been gifted...", "§7" . $reward->getName());
				$player->getLevel()->addSound(new BlazeShootSound($player));
				$callable = $reward->getCallback();
				$callable($player);
				$event->setCancelled();
			}
			if($tag->hasTag(BossEgg::BOSS_ID, IntTag::class)){
				if($player->getLevel()->getFolderName() !== "bossarena"){
					$player->sendMessage(Translation::getMessage("canOnlySpawnInArena"));
					return;
				}
				$areaManager = $this->core->getAreaManager();
				$areas = $areaManager->getAreasInPosition($player->asPosition());
				if($areas !== null){
					foreach($areas as $area){
						if($area->getPvpFlag() === false){
							$player->sendMessage(Translation::getMessage("canOnlySpawnInArena"));
							return;
						}
					}
				}
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$this->core->getCombatManager()->createBoss($tag->getInt(BossEgg::BOSS_ID), $player->getLevel(), Entity::createBaseNBT($player->asPosition()));
				$this->core->getServer()->broadcastMessage(Translation::getMessage("bossSpawned"));
				$event->setCancelled();
			}
			if($tag->hasTag(SellWand::USES, IntTag::class)){
				if(isset($this->sellWandCooldown[$player->getRawUniqueId()]) and (time() - $this->sellWandCooldown[$player->getRawUniqueId()]) < 3){
					$seconds = 3 - (time() - $this->sellWandCooldown[$player->getRawUniqueId()]);
					$player->sendMessage(Translation::getMessage("actionCooldown", [
						"amount" => TextFormat::RED . $seconds
					]));
					return;
				}
				if($event->isCancelled()){
					$player->sendMessage(Translation::getMessage("blockProtected"));
					return;
				}
				$block = $event->getBlock();
				$tile = $block->getLevel()->getTile($block);
				if(!$tile instanceof Container){
					$player->sendMessage(Translation::getMessage("invalidBlock"));
					return;
				}
				$content = $tile->getInventory()->getContents();
				/** @var Item[] $items */
				$items = [];
				$sellable = false;
				$sellables = $this->core->getPriceManager()->getSellables();
				$entries = [];
				foreach($content as $i){
					if(!isset($sellables[$i->getId()])){
						continue;
					}
					$entry = $sellables[$i->getId()];
					if(!$entry->equal($i)){
						continue;
					}
					if($sellable === false){
						$sellable = true;
					}
					if(!isset($entries[$entry->getName()])){
						$entries[$entry->getName()] = $entry;
						$items[$entry->getName()] = $i;
					}else{
						$items[$entry->getName()]->setCount($items[$entry->getName()]->getCount() + $i->getCount());
					}
				}
				if($sellable === false){
					$event->setCancelled();
					$player->sendMessage(Translation::getMessage("nothingSellable"));
					$this->sellWandCooldown[$player->getRawUniqueId()] = time();
					return;
				}
				$price = 0;
				foreach($entries as $entry){
					$i = $items[$entry->getName()];
					$price += $i->getCount() * $entry->getSellPrice();
					$tile->getInventory()->removeItem($i);
					$ev = new ItemSellEvent($player, $i, $price);
					$ev->call();
					$player->sendMessage(Translation::getMessage("sell", [
						"amount" => TextFormat::GREEN . $i->getCount(),
						"item" => TextFormat::DARK_GREEN . $entry->getName(),
						"price" => TextFormat::LIGHT_PURPLE . "$" . $price
					]));
				}
				$player->addToBalance($price);
				$amount = $tag->getInt(SellWand::USES);
				$player->playXpLevelUpSound();
				--$amount;
				if($amount <= 0){
					$player->getLevel()->addSound(new AnvilBreakSound($player));
					$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				}else{
					$tag->setInt(SellWand::USES, $amount);
					$lore = [];
					$lore[] = "";
					$lore[] = TextFormat::RESET . TextFormat::AQUA . "Uses: " . TextFormat::WHITE . $amount;
					$lore[] = "";
					$lore[] = TextFormat::RESET . TextFormat::WHITE . "Tap a chest to sell all It's sellable contents.";
					$item->setLore($lore);
					$inventory->setItemInHand($item);
				}
				$event->setCancelled();
				$this->sellWandCooldown[$player->getRawUniqueId()] = time();
			}
			if($tag->hasTag(HolyBox::SACRED_KIT, StringTag::class)){
				$event->setCancelled();
				if($player->getLevel()->getFolderName() !== $this->core->getServer()->getDefaultLevel()->getFolderName()){
					$player->sendMessage(Translation::getMessage("onlyInSpawn"));
					return;
				}
				$block = $event->getBlock();
				if($block->getId() !== Block::AIR){
					$position = Position::fromObject($event->getBlock()->add(0, 1, 0), $player->getLevel());
					if($player->getLevel()->getBlock($position)->getId() === Block::AIR){
						$inventory->setItemInHand($item->setCount($item->getCount() - 1));
						$faces = [
							0 => 4,
							1 => 2,
							2 => 5,
							3 => 3
						];
						$face = $faces[$player->getDirection()];
						$position->getLevel()->setBlock($position, Block::get(Block::CHEST, $face));
						$this->core->getScheduler()->scheduleRepeatingTask(new HolyBoxAnimationTask($player, $position, $this->core->getKitManager()->getKitByName($tag->getString(HolyBox::SACRED_KIT))), 7);
					}
				}
			}
			if($tag->hasTag(Head::PLAYER, StringTag::class)){
				$player->getLevel()->addSound(new BlazeShootSound($player));
				$amount = $tag->getInt("Balance");
				$name = $tag->getString("Name");
				$player->addToBalance($amount);
				$player->sendMessage("§l§8(§a!§8)§r §7You have received §e$$amount §7from §e$name's §7head.§r");
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				$event->setCancelled();
			}
			if($tag->hasTag(Drops::ITEM_LIST, ListTag::class)){
				$list = $tag->getListTag(Drops::ITEM_LIST);
				$inventory->setItemInHand($item->setCount($item->getCount() - 1));
				foreach($list->getAllValues() as $tag){
					$item = Item::nbtDeserialize($tag);
					if($inventory->canAddItem($item)){
						$inventory->addItem($item);
					}
				}
				$player->getLevel()->addSound(new BlazeShootSound($player->asPosition()));
				$event->setCancelled();
			}
		}
	}

	/**
	 * @priority LOWEST
	 * @param BlockBreakEvent $event
	 */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$item = $event->getItem();
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if(!$player instanceof CrypticPlayer){
			return;
		}
		$blockId = $block->getId();
		if(($level = $item->getEnchantmentLevel(Enchantment::FORTUNE)) > 0){
			if(!in_array($blockId, $this->ids)){
				return;
			}
			$id = 0;
			switch($blockId){
				case Block::COAL_ORE:
					$id = Item::COAL;
					break;
				case Block::DIAMOND_ORE:
					$id = Item::DIAMOND;
					break;
				case Block::EMERALD_ORE:
					$id = Item::EMERALD;
					break;
				case Block::REDSTONE_ORE:
					$id = Item::REDSTONE;
					break;
				case Block::LAPIS_ORE:
					$id = Item::DYE;
					break;
				case Block::NETHER_QUARTZ_ORE:
					$id = Item::NETHER_QUARTZ;
					break;
			}
			$item = Item::get($id, 0, 1 + mt_rand(0, $level + 2));
			if($item->getId() === Item::DYE){
				$item->setDamage(4);
				$item->setCount(2 + mt_rand(0, $level + 2) * 2);
			}
			$drops = [$item];
			$event->setDrops($drops);
		}
	}

	/**
	 * @priority HIGHEST
	 * @param EntityDamageEvent $event
	 */
	public function onEntityDamage(EntityDamageEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		if($event instanceof EntityDamageByEntityEvent){
			$damager = $event->getDamager();
			if(!$damager instanceof CrypticPlayer){
				return;
			}
			$item = $damager->getInventory()->getItemInHand();
			if(($level = $item->getEnchantmentLevel(Enchantment::LOOTING)) <= 0){
				return;
			}
			/** @var Living $entity */
			$entity = $event->getEntity();
			if($entity instanceof CrypticPlayer){
				return;
			}
			if($event->getFinalDamage() >= $entity->getHealth()){
				foreach($entity->getDrops() as $drop){
					$drop->setCount($drop->getCount() + mt_rand(1, $level));
					$entity->getLevel()->dropItem($entity, $drop);
				}
			}
		}
	}

	/**
	 * @priority HIGHEST
	 * @param InventoryTransactionEvent $event
	 */
	public function onInventoryTransaction(InventoryTransactionEvent $event){
		$transaction = $event->getTransaction();
		foreach($transaction->getActions() as $action){
			if($action instanceof SlotChangeAction){
				$inventory = $action->getInventory();
				if($action->getSourceItem()->hasEnchantments() or $action->getTargetItem()->hasEnchantments()){
					if($inventory instanceof ArmorInventory){
						$holder = $inventory->getHolder();
						if($holder instanceof CrypticPlayer){
							$this->core->getScheduler()->scheduleDelayedTask(new class($holder) extends Task{

								/** @var CrypticPlayer */
								private $player;

								/**
								 *  constructor.
								 *
								 * @param CrypticPlayer $player
								 */
								public function __construct(CrypticPlayer $player){
									$this->player = $player;
								}

								/**
								 * @param int $currentTick
								 */
								public function onRun(int $currentTick){
									if($this->player->isOnline()){
										$this->player->setActiveArmorEnchantments();
									}
								}
							}, 1);
						}
						return;
					}
				}
			}
		}
	}
}
