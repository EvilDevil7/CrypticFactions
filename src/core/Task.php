<?php

declare(strict_types=1);

namespace core;

use core\discord\DiscordManager;
use core\faction\Faction;
use core\rank\Rank;
use core\sessions\SessionManager;
use core\task\PlayerKickTask;
use core\translation\Translation;
use core\translation\TranslationException;
use kim\present\inventorymonitor\inventory\SyncInventory;
use pocketmine\block\Block;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\LeavesDecayEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExperienceChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\item\Item;
use pocketmine\level\particle\Particle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\ItemSpawnEvent;

class CrypticListener implements Listener
{

    /** @var Cryptic */
    private $core;

    /** @var int */
    private $count = 0;

    /** @var string[] */
    private $messages = [];

    /** @var int[] */
    private $chat = [];

    /** @var int[] */
    private $command = [];

    /**
     * CrypticListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core)
    {
        $this->core = $core;
        $this->messages[] = Cryptic::SERVER_NAME;
        $this->messages = array_merge($this->messages, Cryptic::MESSAGES);
    }

    /**
     * @param CommandEvent $e
     */
    public function onCommandUse(CommandEvent $e): void
    {
        /** @var CrypticPlayer $player */
        $player = $e->getSender();
        if ($player->getRank()->getIdentifier() >= 8 and !in_array($player->getRank()->getIdentifier(), [Rank::OVERLORD, Rank::YOUTUBER, Rank::FAMOUS])) {
            $webhook = "692900412038316075/PFLhQ9OJYUAGoj6EGjmMa7X_oGGrNcsSyozAS8Z9JmVK4SA5FGOI-oWorPt05blTWCAO";
            DiscordManager::postWebhook($webhook, "/" . $e->getCommand(), $player->getName());
        }
    }

    /**
     * @param PlayerDropItemEvent $e
     */
    public function onDrop(PlayerDropItemEvent $e): void
    {
        if (!$e->isCancelled() and $e->getPlayer()->getLevel()->getFolderName() == Cryptic::WORLD and !$e->getPlayer()->isOp()) {
            $e->setCancelled();
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerLoginEvent $event
     */
    public function onPlayerLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player instanceof CrypticPlayer) {
            return;
        }
        $player->load($this->core);
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        SessionManager::close($event->getPlayer());
    }

    /**
     * @priority LOWEST
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void
    {
        $event->setJoinMessage("");
        $player = $event->getPlayer();
        if (!$player instanceof CrypticPlayer) {
            return;
        }

        SessionManager::open($player);
        $server = $this->core->getServer();
        $players = count($server->getOnlinePlayers());
        $maxPlayers = $this->core->getServer()->getMaxPlayers();
        $max = $maxPlayers - Cryptic::EXTRA_SLOTS;

        if ($players >= $max) {
            if (!$player->hasPermission("permission.join.full") or $player->getRank()->getIdentifier() < Rank::TRAINEE) {
                $this->core->getScheduler()->scheduleDelayedTask(new PlayerKickTask($player), 40);
                return;
            }
        }
        /** @var CrypticPlayer $onlinePlayer */
        foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if ($player->getRank()->getIdentifier() >= Rank::TRAINEE and $player->getRank()->getIdentifier() <= Rank::OWNER) {
                break;
            }
            if ($onlinePlayer->hasVanished()) {
                $player->hidePlayer($onlinePlayer);
            }
        }
        if ($player->getCurrentTotalXp() > 0x7fffffff) {
            $player->setCurrentTotalXp(0x7fffffff);
        }
        if ($player->getCurrentTotalXp() < -0x80000000) {
            $player->setCurrentTotalXp(0);
        }
        $this->core->getScheduler()->scheduleDelayedTask(new class($player) extends Task
        {

            /** @var CrypticPlayer */
            private $player;

            /**
             *  constructor.
             *
             * @param CrypticPlayer $player
             */
            public function __construct(CrypticPlayer $player)
            {
                $this->player = $player;
            }

            /**
             * @param int $currentTick
             */
            public function onRun(int $currentTick)
            {
                if ($this->player->isOnline() === false) {
                    return;
                }
                $item = $this->player->getInventory()->getItemInHand();
                $this->player->getInventory()->setItemInHand(Item::get(Item::TOTEM));
                $pk = new LevelEventPacket();
                $pk->position = $this->player;
                $pk->evid = LevelEventPacket::EVENT_SOUND_TOTEM;
                $pk->data = 0;
                $this->player->sendDataPacket($pk);
                $pk = new LevelEventPacket;
                $pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | (Particle::TYPE_TOTEM & 0xFFF);
                $pk->position = $this->player;
                $pk->data = 0;
                $this->player->sendDataPacket($pk);
                $pk = new ActorEventPacket();
                $pk->entityRuntimeId = $this->player->getId();
                $pk->event = ActorEventPacket::CONSUME_TOTEM;
                $pk->data = 0;
                $this->player->sendDataPacket($pk);
                $this->player->addTitle("  ", "§l§6Cryptic§ePE§r\n§b" . Cryptic::GAMEMODE . "\n\n\n\n\n\n\n", 5, 20, 5);
                $this->player->getInventory()->setItemInHand($item);
//				if(count($this->player->getInbox()->getInventory()->getContents()) >= 1){
//					$this->player->sendMessage(Translation::getMessage("inboxAlert"));
//				}
            }
        }, 40);
    }

    /**
     * @param ItemSpawnEvent $e
     */
    public function onThrow(ItemSpawnEvent $e)
    {
        $entity = $e->getEntity();
        $item = $entity->getItem();
        $name = $item->getName();
        $count = $item->getCount();
        $entity->setNameTag("§7" . $name . " §r§8[§l§ex" . $count . "§r§8]§r");
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
    }

    /**
     * @priority HIGHEST
     * @param PlayerExperienceChangeEvent $event
     */
    public function onPlayerExperienceChange(PlayerExperienceChangeEvent $event): void
    {
        $player = $event->getEntity();
        if (!$player instanceof CrypticPlayer) {
            return;
        }
        if ($player->getCurrentTotalXp() > 0x7fffffff or $player->getCurrentTotalXp() < -0x80000000) {
            $event->setCancelled();
        }
    }

    /**
     * @priority NORMAL
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        $event->setQuitMessage("");
    }

    /**
     * @priority NORMAL
     * @param PlayerCreationEvent $event
     */
    public function onPlayerCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(CrypticPlayer::class);
    }

    /**
     * @priority LOWEST
     * @param PlayerChatEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        if (!$player instanceof CrypticPlayer) {
            return;
        }
        if ($player->getRank()->getIdentifier() >= Rank::OVERLORD) {
            return;
        }
        if ($player->getRank()->getIdentifier() < 8) {
            if (!isset($this->chat[$player->getRawUniqueId()])) {
                $this->chat[$player->getRawUniqueId()] = time();
                return;
            }
            if (time() - $this->chat[$player->getRawUniqueId()] >= 3) {
                $this->chat[$player->getRawUniqueId()] = time();
                return;
            }
            $seconds = 3 - (time() - $this->chat[$player->getRawUniqueId()]);
            $player->sendMessage(Translation::getMessage("actionCooldown", [
                "amount" => TextFormat::RED . $seconds
            ]));
            $event->setCancelled();
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerCommandPreprocessEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player instanceof CrypticPlayer) {
            return;
        }
        if ($this->core->getAnnouncementManager()->getRestarter()->getRestartProgress() > 5) {
            if (strpos($event->getMessage(), "/") !== 0) {
                return;
            }
            if ($player->getRank()->getIdentifier() > 8) {
                return;
            }
            if (!isset($this->command[$player->getRawUniqueId()])) {
                $this->command[$player->getRawUniqueId()] = time();
                return;
            }
            if (time() - $this->command[$player->getRawUniqueId()] >= 3) {
                $this->command[$player->getRawUniqueId()] = time();
                return;
            }
            $seconds = 3 - (time() - $this->command[$player->getRawUniqueId()]);
            $player->sendMessage(Translation::getMessage("actionCooldown", [
                "amount" => TextFormat::RED . $seconds
            ]));
            $event->setCancelled();
            return;
        }
        $event->setCancelled();
        $player->sendMessage(Translation::getMessage("restartingSoon"));
    }

    /**
     * @priority LOWEST
     * @param PlayerMoveEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $level = $player->getLevel();
        if ($level->getName() !== Faction::CLAIM_WORLD) {
            return;
        }
        $x = abs($player->getFloorX());
        $y = abs($player->getFloorY());
        $z = abs($player->getFloorZ());
        $message = Translation::getMessage("borderReached");
        if ($x >= Cryptic::BORDER) {
            $player->teleport(new Vector3($x - 1, $y, Cryptic::BORDER - 1));
            $player->sendMessage($message);
        }
        if ($z >= Cryptic::BORDER) {
            $player->teleport(new Vector3($x, $y, Cryptic::BORDER - 1));
            $player->sendMessage($message);
        }
        if ($x >= Cryptic::BORDER and abs($z) >= Cryptic::BORDER) {
            $player->teleport(new Vector3(Cryptic::BORDER - 1, $y, Cryptic::BORDER - 1));
            $player->sendMessage($message);
        }
    }

    /**
     * @priority NORMAL
     * @param QueryRegenerateEvent $event
     */
    public function onQueryRegenerate(QueryRegenerateEvent $event): void
    {
        $this->core->getServer()->getNetwork()->setName($this->messages[$this->count++ % count($this->messages)]);
        $maxPlayers = $this->core->getServer()->getMaxPlayers();
        $maxSlots = $maxPlayers - Cryptic::EXTRA_SLOTS;
        $players = count($this->core->getServer()->getOnlinePlayers());
        if ($players === $maxPlayers) {
            $event->setMaxPlayerCount($maxPlayers);
            return;
        }
        if ($maxSlots <= $players) {
            if ($players === $maxSlots) {
                $event->setMaxPlayerCount($maxSlots + 1);
                return;
            }
            $event->setMaxPlayerCount($maxSlots + $players + 1);
            return;
        }
        $event->setMaxPlayerCount($maxSlots);
    }

    /**
     * @priority LOWEST
     * @param BlockBreakEvent $event
     * @throws TranslationException
     */
    public function onBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $event->setCancelled();
                return;
            }
            if ($player->canAutoSell() && $player->isAutoSelling()) {
                $player->autoSell();
            }
            if ($player->isOp()) {
                return;
            }
            $level = $event->getBlock()->getLevel();
            if ($level->getName() !== Faction::CLAIM_WORLD) {
                $event->setCancelled();
                return;
            }
        }
    }

    /**
     * @priority LOWEST
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $event->setCancelled();
                return;
            }
            if ($player->isOp()) {
                return;
            }
            $level = $event->getBlock()->getLevel();
            if ($level->getName() !== Faction::CLAIM_WORLD) {
                $event->setCancelled();
                return;
            }
        }
    }

    /**
     * @priority NORMAL
     * @param EntityLevelChangeEvent $event
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event): void
    {
        $entity = $event->getEntity();
        if (!$entity instanceof CrypticPlayer) {
            return;
        }
        foreach ($entity->getFloatingTexts() as $floatingText) {
            if ($floatingText->isInvisible() and $event->getTarget()->getName() === $floatingText->getLevel()->getName()) {
                $floatingText->spawn($entity);
                continue;
            }
            if ((!$floatingText->isInvisible()) and $event->getTarget()->getName() !== $floatingText->getLevel()->getName()) {
                $floatingText->despawn($entity);
                continue;
            }
        }
        foreach ($this->core->getEntityManager()->getNPCs() as $npc) {
            if ($npc->getPosition()->getLevel()->getName() !== $entity->getName()) {
                $npc->despawnFrom($entity);
            } else {
                $npc->spawnTo($entity);
            }
        }
    }

    public function onLeaveDecay(LeavesDecayEvent $event): void
    {
        $event->setCancelled();
    }

//    /**
//     * @param EntityDamageEvent $e
//     */
//    public function onHeadTrade(EntityDamageEvent $e): void{
//        $entity = $e->getEntity();
//
//        if($e instanceof EntityDamageByEntityEvent){
//            $damager = $e->getDamager();
//
//            if($entity instanceof SlapperHuman){
//                if($entity->getNameTag() == "trade"){
//                    if($damager instanceof Player){
//                        $max = 10;
//                        $cfg = Cryptic::getInstance()->getHead();
//                        if(!isset($data[$damager->getName()])){
//                            $cfg->set($damager->getName(), 0);
//                            $cfg->save();
//                        }
//                        $data = $cfg->getAll();
//                        $head = $data[$damager->getName()];
//                        $item = $damager->getInventory()->getItemInHand();
//                        if($item->getId() == Item::SKULL and $item->getNamedTag()->hasTag("head")){
//                            if($head >= $max){
//                                $damager->sendMessage(TextFormat::RED . "You've already maxed out collection, try click without head item.");
//                                return;
//                            }
//                            $cfg->set($damager->getName(), ($head + 1));
//                            $cfg->save();
//                            $head += 1;
//                            $damager->sendMessage(TextFormat::GREEN . "You've collected " . $head . " heads!");
//                            $damager->getInventory()->setItemInHand(Item::get(Item::AIR));
//                            return;
//                        }
//                        if($head >= $max){
//                            $cfg->set($damager->getName(), 0);
//                            $cfg->save();
//                            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(), "mask charm " . $damager->getName());
//                            $damager->sendMessage(TextFormat::GREEN . "Thanks for the collection! You've received a Mask Charm!");
//                        }else{
//                            $damager->sendMessage(TextFormat::AQUA . "You've collected " . $head . " / " . $max);
//                        }
//                    }
//                }
//            }
//        }
//    }

    public function onArmorInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($event->getAction() !== PlayerInteractEvent::RIGHT_CLICK_AIR) {
            return;
        }
        $item = $player->getInventory()->getItemInHand();
        if ($player->getArmorInventory()->getHelmet()->getId() === Item::AIR && in_array($item->getId(), [Item::LEATHER_CAP, Item::CHAIN_HELMET, Item::IRON_HELMET, Item::GOLD_BOOTS, Item::DIAMOND_HELMET, Item::MOB_HEAD, Item::TURTLE_HELMET])) {
            if ($player->isCreative()) return;
            if ($event->isCancelled()) return;
            $helmet = Item::get($item->getId(), $item->getDamage());
            foreach ($item->getEnchantments() as $enchantment) {
                $helmet->addEnchantment($enchantment);
            }
            $helmet->setCustomName($item->hasCustomName() ? $item->getCustomName() : $item->getName());
            $helmet->setLore($item->getLore());
            $player->getArmorInventory()->setHelmet($helmet);
            $player->getInventory()->setItemInHand(Item::get(Item::AIR));
        } elseif ($player->getArmorInventory()->getChestplate()->getId() === Item::AIR && in_array($item->getId(), [Item::LEATHER_CHESTPLATE, Item::CHAIN_CHESTPLATE, Item::IRON_CHESTPLATE, Item::GOLD_CHESTPLATE, Item::DIAMOND_CHESTPLATE, Item::ELYTRA])) {
            if ($player->isCreative()) return;
            if ($event->isCancelled()) return;
            $chestplate = Item::get($item->getId(), $item->getDamage());
            foreach ($item->getEnchantments() as $enchantment) {
                $chestplate->addEnchantment($enchantment);
            }
            $chestplate->setCustomName($item->hasCustomName() ? $item->getCustomName() : $item->getName());
            $chestplate->setLore($item->getLore());
            $player->getArmorInventory()->setChestplate($chestplate);
            $player->getInventory()->setItemInHand(Item::get(Item::AIR));
        } elseif ($player->getArmorInventory()->getLeggings()->getId() === Item::AIR && in_array($item->getId(), [Item::LEATHER_LEGGINGS, Item::CHAIN_LEGGINGS, Item::IRON_LEGGINGS, Item::GOLD_LEGGINGS, Item::DIAMOND_LEGGINGS])) {
            if ($player->isCreative()) return;
            if ($event->isCancelled()) return;
            $leggings = Item::get($item->getId(), $item->getDamage());
            foreach ($item->getEnchantments() as $enchantment) {
                $leggings->addEnchantment($enchantment);
            }
            $leggings->setCustomName($item->hasCustomName() ? $item->getCustomName() : $item->getName());
            $leggings->setLore($item->getLore());
            $player->getArmorInventory()->setLeggings($leggings);
            $player->getInventory()->setItemInHand(Item::get(Item::AIR));
        } elseif ($player->getArmorInventory()->getBoots()->getId() === Item::AIR && in_array($item->getId(), [Item::LEATHER_BOOTS, Item::CHAIN_BOOTS, Item::IRON_BOOTS, Item::GOLD_BOOTS, Item::DIAMOND_BOOTS])) {
            if ($player->isCreative()) return;
            if ($event->isCancelled()) return;
            $boots = Item::get($item->getId(), $item->getDamage());
            foreach ($item->getEnchantments() as $enchantment) {
                $boots->addEnchantment($enchantment);
            }
            $boots->setCustomName($item->hasCustomName() ? $item->getCustomName() : $item->getName());
            $boots->setLore($item->getLore());
            $player->getArmorInventory()->setBoots($boots);
            $player->getInventory()->setItemInHand(Item::get(Item::AIR));
        }
    }

    /**
     * @param BlockPlaceEvent $e
     */
    public function onPlaceHead(BlockPlaceEvent $e): void
    {
        if ($e->getItem()->getId() == Item::SKULL) {
            $e->setCancelled();
        }
    }

    public function onInvTransaction(InventoryTransactionEvent $event): void
    {
        $player = $event->getTransaction()->getSource();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $event->setCancelled();
                return;
            }
        }
    }

    public function onItemDrop(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $event->setCancelled();
                return;
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     * @throws TranslationException
     */
    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $item = $event->getItem();
                $block = $event->getBlock();
                if ($block->getId() === Block::CHEST) {
                    $event->setCancelled();
                    return;
                }
                switch ($item->getId()) {
                    case Item::CONCRETE:
                        if ($item->getDamage() === 5) {
                            $player->setChatMode(CrypticPlayer::PUBLIC);
                            $player->getInventory()->setItem(1, Item::get(Item::CONCRETE, 14, 1)->setCustomName(TextFormat::ITALIC . TextFormat::RED . "Staff Chat"));
                            $player->sendMessage(Translation::getMessage("chatModeSwitch", [
                                "mode" => TextFormat::GREEN . strtoupper($player->getChatModeToString())
                            ]));
                        } elseif ($item->getDamage() === 14) {
                            $player->setChatMode(CrypticPlayer::STAFF);
                            $player->getInventory()->setItem(1, Item::get(Item::CONCRETE, 5, 1)->setCustomName(TextFormat::ITALIC . TextFormat::GREEN . "Staff Chat"));
                            $player->sendMessage(Translation::getMessage("chatModeSwitch", [
                                "mode" => TextFormat::GREEN . strtoupper($player->getChatModeToString())
                            ]));
                        }
                        break;
                    case Item::ICE:
                        $player->sendMessage("§l§8(§a!§8)§r §7You must tap a player with this item to freeze/unfreeze them!");
                        break;
                    case Item::MOB_HEAD:
                        $event->setCancelled();
                        $randomPlayer = $this->core->getServer()->getOnlinePlayers()[array_rand($this->core->getServer()->getOnlinePlayers())];
                        if ($randomPlayer instanceof CrypticPlayer) {
                            $player->teleport($randomPlayer->asPosition());
                            $player->sendMessage("§l§8(§a!§8)§r §7You have teleported to " . TextFormat::GREEN . $randomPlayer->getName() . "§r§7.");
                        }
                        break;
                    case Item::BOOK:
                        $player->sendMessage("§l§8(§a!§8)§r §7You must tap a player with this item to see their inventory!");
                        break;
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof CrypticPlayer) {
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof CrypticPlayer) {
                    if ($damager->isInStaffMode()) {
                        $event->setCancelled();
                        switch ($damager->getInventory()->getItemInHand()->getId()) {
                            case Item::ICE:
                                $entity->setImmobile(!$entity->isImmobile());
                                $damager->sendMessage($entity->isImmobile() ? "§l§8(§a!§8)§r §7You have successfully §l§aENABLED§r §7freeze on " . TextFormat::GOLD . $entity->getName() . "§7!" : "§l§8(§a!§8)§r §7You have successfully §l§cDISABLED§r §7freeze on " . TextFormat::GOLD . $entity->getName() . "§7!");
                                break;
                            case Item::BOOK:
                                $damager->addWindow(SyncInventory::load($entity->getName()));
                                break;
                        }
                    }
                }
            }
        }
    }

    public function onCommandPreProcess(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if (substr($event->getMessage(), 0, 1) === "/") {
                $command = substr(explode(" ", $event->getMessage())[0], 1);
                if (strtolower($command) === "tp" or strtolower($command) === "teleport") {
                    if ($player->isInStaffMode()) {
                        $player->sendMessage("§l§8(§a!§8)§r §7You can not use this while in staff mode!");
                        $event->setCancelled();
                        return;
                    }
                }
            }
        }
    }

    public function onStaffModeQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();
        if ($player instanceof CrypticPlayer) {
            if ($player->isInStaffMode()) {
                $player->setStaffMode(false);
            }
        }
    }
}
