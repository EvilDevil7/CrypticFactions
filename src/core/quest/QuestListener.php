<?php

declare(strict_types = 1);

namespace core\quest;

use core\envoy\event\EnvoyClaimEvent;
use core\Cryptic;
use core\CrypticPlayer;
use core\price\event\ItemBuyEvent;
use core\price\event\ItemSellEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;

class QuestListener implements Listener {

    /** @var Cryptic */
    private $core;

    /**
     * CrypticListener constructor.
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
        $this->core->getQuestManager()->addSession($player);
        $session = $this->core->getQuestManager()->getSession($player);
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($session->getQuestProgress($quest) === null) {
                $session->addQuestProgress($quest);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $cause = $player->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent) {
            if($cause->getDamager() instanceof CrypticPlayer) {
                foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
                    if($quest->getEventType() === Quest::KILL) {
                        $callable = $quest->getCallable();
                        $callable($cause);
                    }
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        if($event->isCancelled()) {
            return;
        }
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::DAMAGE) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        if($event->isCancelled()) {
            return;
        }
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::BREAK) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param BlockPlaceEvent $event
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        if($event->isCancelled()) {
            return;
        }
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::PLACE) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param ItemSellEvent $event
     */
    public function onItemSell(ItemSellEvent $event): void {
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::SELL) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param ItemBuyEvent $event
     */
    public function onItemBuy(ItemBuyEvent $event): void {
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::BUY) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param EnvoyClaimEvent $event
     */
    public function onEnvoyClaim(EnvoyClaimEvent $event): void {
        foreach($this->core->getQuestManager()->getActiveQuests() as $quest) {
            if($quest->getEventType() === Quest::CLAIM_ENVOY) {
                $callable = $quest->getCallable();
                $callable($event);
            }
        }
    }
}