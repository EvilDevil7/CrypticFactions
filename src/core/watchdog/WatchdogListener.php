<?php

declare(strict_types = 1);

namespace core\watchdog;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use core\watchdog\task\ProxyCheckTask;
use core\libs\muqsit\invmenu\InvMenu;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\tile\Container;
use pocketmine\utils\TextFormat;

class WatchdogListener implements Listener {

    /** @var Cryptic */
    private $core;

    /** @var string[] */
    private $keys = [
      // Yall arent using my proxy keys where i created 5 accounts with my email ;-; 
      // Love david
    ];

    /** @var int */
    private $count = 0;

    /** @var int */
    private $autoClickTime = 0;

    /**
     * WatchdogListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @priority LOWEST
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
       /*$ipAddress = $player->getAddress();
        $uuid = $player->getRawUniqueId();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT riskLevel FROM ipAddress WHERE ipAddress = ? AND uuid = ?");
        $stmt->bind_param("ss", $ipAddress, $uuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if($result === null) {
            ++$this->count;
            if($this->count > count($this->keys) - 1) {
                $this->count = 0;
            }
            $key = $this->keys[$this->count++];
            $this->core->getServer()->getAsyncPool()->submitTaskToWorker(new ProxyCheckTask($player->getName(), $ipAddress, $key), 0);
            return;
        }
        if($result === 1) {
            $player->close(null, TextFormat::RED . "A malicious ip swapper was detected!");
            return;
            */
    }

    /**
     * @priority NORMAL
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($player->isImmobile()) {
            $name = $player->getName();
            $reason = "Leaving while being frozen";
            $time = 604800;
            $this->core->getServer()->dispatchCommand(new ConsoleCommandSender(), "tempban $name $time $reason");
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerCommandPreprocessEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($player->isImmobile()) {
            $message = $event->getMessage();
            $value = false;
            $commands = ["/msg", "/w", "/tell", "/whisper", "/message", "/pm", "/m"];
            foreach($commands as $command) {
                if(strpos($message, $command) !== false) {
                    $value = true;
                }
            }
            if($value === true) {
                $player->sendMessage(Translation::getMessage("frozen", [
                    "name" => "You are"
                ]));
            }
        }
    }

    /**
     * @priority HIGH
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($player->hasVanished()) {
            $container = $event->getBlock()->getLevel()->getTile($event->getBlock());
            if($container instanceof Container) {
                $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $menu->readonly();
                $menu->getInventory($player)->setContents($container->getInventory()->getContents());
                $menu->setName($container->getInventory()->getName());
                $menu->send($player);
            }
            $event->setCancelled();
        }
    }

    /**
     * @priority LOWEST
     * @param EntityDamageEvent $event
     *
     * @throws TranslationException
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        if($entity->hasVanished()) {
            $event->setCancelled();
        }
        if($entity->isImmobile()) {
            $event->setCancelled();
            $entity->sendMessage(Translation::getMessage("frozen", [
                "name" => "You are"
            ]));
            if($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if(!$damager instanceof CrypticPlayer) {
                    return;
                }
                $damager->sendMessage(Translation::getMessage("frozen", [
                    "name" => $entity->getName() . " is"
                ]));
            }
        }
    }

    /**
     * @priority NORMAL
     * @param DataPacketReceiveEvent $event
     *
     * @throws TranslationException
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($packet instanceof InventoryTransactionPacket) {
            if($packet->transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                ++$player->cps;
                if($this->autoClickTime !== time()) {
                    $multiplier = $this->autoClickTime - time();
                    if($multiplier >= 1) {
                        foreach($this->core->getServer()->getOnlinePlayers() as $player) {
                            if($player instanceof CrypticPlayer) {
                                if($player->cps > (30 * $multiplier)) {
                                    $cps = floor($player->cps / $multiplier);
                                    $this->core->getServer()->broadcastMessage(Translation::getMessage("kickBroadcast", [
                                        "name" => $player->getName(),
                                        "effector" => "Watchdog",
                                        "reason" => "Auto-clicking. CPS: $cps"
                                    ]));
                                    $player->close(null, Translation::getMessage("kickMessage", [
                                        "name" => "Watchdog",
                                        "reason" => "Auto-clicking. CPS: $cps"
                                    ]));
                                }
                                $player->cps = 0;
                            }
                        }
                        $this->autoClickTime = time();
                    }
                }
            }
        }
    }
}
