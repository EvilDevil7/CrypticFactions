<?php

declare(strict_types = 1);

namespace core\entity;

use core\Cryptic;
use core\CrypticPlayer;
use core\price\event\ItemSellEvent;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityInventoryChangeEvent;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class EntityListener implements Listener {

    /** @var Cryptic */
    private $core;

    /** @var Block[] */
    private $blocks = [];

    /** @var Entity[] */
    private $entities = [];

    /** @var string[] */
    private $ids = [];

    /**
     * CrypticListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @priority HIGHEST
     *
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $id = $block->getId();
        if((!$id === Block::BEDROCK) or (!$id === Block::OBSIDIAN)) {
            return;
        }
        if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK and $id === Block::BEDROCK) {
            $this->core->getScheduler()->scheduleDelayedTask(new class($player, $block) extends Task {

                /** @var CrypticPlayer */
                private $player;

                /** @var Block */
                private $block;

                /**
                 *  constructor.
                 *
                 * @param CrypticPlayer $player
                 * @param Block         $block
                 */
                public function __construct(CrypticPlayer $player, Block $block) {
                    $this->player = $player;
                    $this->block = $block;
                }

                /**
                 * @param int $currentTick
                 */
                public function onRun(int $currentTick) {
                    if($this->player->isClosed()) {
                        return;
                    }
                    if($this->player->isBreaking() === true) {
                        $item = $this->player->getInventory()->getItemInHand();
                        $this->player->getLevel()->useBreakOn($this->block, $item, $this->player, true);
                    }
                }
            }, (int)(($block->getBreakTime($player->getInventory()->getItemInHand()) * 20) - 1));
        }
        $hash = Level::blockHash($block->x, $block->y, $block->z);
        if(!isset($this->blocks[$hash])) {
            if($id === Block::BEDROCK) {
                $this->blocks[$hash] = 0;
            }
            if($id === Block::OBSIDIAN) {
                $this->blocks[$hash] = 0;
            }
            return;
        }
        if($id === Block::BEDROCK) {
            $durability = 50 - $this->blocks[$hash];
            $player->sendPopup(TextFormat::RED . "Durability: " . $durability);
            return;
        }
        $durability = 10 - $this->blocks[$hash];
        $player->sendPopup(TextFormat::RED . "Durability: " . $durability);
    }

    /**
     * @priority HIGHEST
     *
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $block = $event->getBlock();
        if($block->getId() === Block::BEDROCK or $block->getId() === Block::OBSIDIAN) {
            $hash = Level::blockHash($block->x, $block->y, $block->z);
            if(isset($this->blocks[$hash])) {
                unset($this->blocks[$hash]);
            }
        }
        $drops = $event->getDrops();
        $player = $event->getPlayer();
        $inventory = $player->getInventory();
        foreach($drops as $drop) {
            if(!$inventory->canAddItem($drop)) {
                $event->setXpDropAmount(0);
                $player->addTitle(TextFormat::DARK_RED . "Full Inventory", TextFormat::RED . "Clear out your inventory!");
                $event->setCancelled();
                return;
            }else{
                $event->getPlayer()->addXp($event->getXpDropAmount());
                $event->setXpDropAmount(0);
            }
        }
        foreach($drops as $drop) {
            $inventory->addItem($drop);
        }
        $event->setDrops([]);
    }

    /**
     * @priority HIGHEST
     *
     * @param EntityExplodeEvent $event
     */
    public function onEntityExplode(EntityExplodeEvent $event) {
        $list = array_filter($event->getBlockList(), function(Block $block): ?Block {
            if($this->core->getFactionManager()->getClaimInPosition($block->asPosition()) === null) {
                return null;
            }
            if($block->getId() !== Block::BEDROCK and $block->getId() !== Block::OBSIDIAN) {
                return $block;
            }
            if($block->getY() <= 0) {
                return null;
            }
            $hash = Level::blockHash($block->x, $block->y, $block->z);
            if(!isset($this->blocks[$hash])) {
                $this->blocks[$hash] = 0;
            }
            $this->blocks[$hash]++;
            if($block->getId() === Block::BEDROCK) {
                if(isset($this->blocks[$hash]) and $this->blocks[$hash] >= 50) {
                    unset($this->blocks[$hash]);
                    return $block;
                }
                return null;
            }
            if($block->getId() === Block::OBSIDIAN) {
                if(isset($this->blocks[$hash]) and $this->blocks[$hash] >= 10) {
                    unset($this->blocks[$hash]);
                    $block->getLevel()->setBlock($block, Block::get(Block::AIR));
                    return $block;
                }
            }
            return null;
        });
        $list = array_filter($list);
        $event->setBlockList($list);
    }

    /**
     * @priority HIGHEST
     *
     * @param EntitySpawnEvent $event
     */
    public function onEntitySpawn(EntitySpawnEvent $event) {
        $entity = $event->getEntity();
        if($entity instanceof ExperienceOrb) {
            $entity->flagForDespawn();
            return;
        }
        if($entity instanceof Human) {
            return;
        }
        $uuid = uniqid();
        if($entity instanceof Living or $entity instanceof ItemEntity) {
            if(count($this->entities) > 250) {
                $despawn = array_shift($this->entities);
                if(!$despawn->isClosed()) {
                    $despawn->flagForDespawn();
                }
            }
            $this->ids[$entity->getId()] = $uuid;
            $this->entities[$uuid] = $entity;
            if(EntityManager::canStack($entity)) {
                EntityManager::addToStack($entity);
            }
        }
    }

    /**
     * @priority LOWEST
     *
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if($event instanceof EntityDamageByEntityEvent) {
            if($entity->namedtag->hasTag(EntityManager::STACK_TAG) and $entity instanceof Living) {
                if($entity->getHealth() <= $event->getFinalDamage()) {
                    $damager = $event->getDamager();
                    if($damager instanceof CrypticPlayer) {
                        $damager->addXp($entity->getXpDropAmount());
                    }
                    EntityManager::decreaseStackSize($entity);
                    $event->setCancelled();
                }
                else {
                    $event->setKnockBack(0);
                }
            }
        }
    }

    /**
     * @priority HIGHEST
     *
     * @param EntityDespawnEvent $event
     */
    public function onEntityDespawn(EntityDespawnEvent $event): void {
        $entity = $event->getEntity();
        if(!isset($this->ids[$entity->getId()])) {
            return;
        }
        $uuid = $this->ids[$entity->getId()];
        unset($this->ids[$entity->getId()]);
        if(isset($this->entities[$uuid])) {
            unset($this->entities[$uuid]);
        }
    }

    /**
     * @priority NORMAL
     * @param DataPacketReceiveEvent $event
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        $player = $event->getPlayer();
        if($packet instanceof PlayerActionPacket) {
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            $action = $packet->action;
            if($action === PlayerActionPacket::ACTION_START_BREAK) {
                $player->setBreaking();
            }
            if($action === PlayerActionPacket::ACTION_ABORT_BREAK or $action === PlayerActionPacket::ACTION_STOP_BREAK) {
                $player->setBreaking(false);
            }
        }
    }

    /**
     * @priority NORMAL
     * @param EntityInventoryChangeEvent $event
     *
     * @throws TranslationException
     */
    public function onEntityInventoryChange(EntityInventoryChangeEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        if($entity->isAutoSelling() === false) {
            return;
        }
        if($entity->canAutoSell() === false) {
            return;
        }
        $entity->autoSell();
    }
}
