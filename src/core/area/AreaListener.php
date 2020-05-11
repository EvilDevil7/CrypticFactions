<?php

declare(strict_types = 1);

namespace core\area;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\block\Block;
use pocketmine\block\EnderChest;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\level\Position;

class AreaListener implements Listener {

    /** @var Cryptic */
    private $core;

    /**
     * AreaListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @priority HIGH
     * @param PlayerMoveEvent $event
     */
    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $to = $event->getTo();
        if($to->getY() < 0) {
            if($to->getLevel()->getFolderName() === $this->core->getServer()->getDefaultLevel()->getFolderName()) {
                $player->teleport($to->getLevel()->getSpawnLocation());
                return;
            }
            $pos = new Position($to->getX(), 0, $to->getZ(), $to->getLevel());
            $to->getLevel()->setBlock($pos, Block::get(Block::BEDROCK));
            $player->teleport(Position::fromObject($pos->add(0, 1, 0), $pos->getLevel()));
        }
    }

    /**
     * @priority HIGH
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if((!$player instanceof CrypticPlayer) or $player->isOp()) {
            return;
        }
        $block = $event->getBlock();
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($block->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getEditFlag() === false and !$block instanceof EnderChest) {
                    $event->setCancelled();
                    return;
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerExhaustEvent $event
     */
    public function onPlayerExhaust(PlayerExhaustEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($player->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getPvpFlag() === false) {
                    $event->setCancelled();
                    return;
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param BlockBreakEvent $event
     *
     * @throws TranslationException
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if($block->getY() <= 0) {
            $event->setCancelled();
            return;
        }
        if((!$player instanceof CrypticPlayer) or $player->isOp()) {
            return;
        }
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($block->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getEditFlag() === false) {
                    $player->sendMessage(Translation::getMessage("noPermission"));
                    $event->setCancelled();
                    return;
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param BlockPlaceEvent $event
     *
     * @throws TranslationException
     */
    public function onBlockPlace(BlockPlaceEvent $event): void {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if($player->isOp()) {
            return;
        }
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($block->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getEditFlag() === false) {
                    $player->sendMessage(Translation::getMessage("noPermission"));
                    $event->setCancelled();
                    return;
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($entity->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getPvpFlag() === false) {
                    $event->setCancelled();
                    return;
                }
            }
        }
    }

    /**
     * @priority LOWEST
     * @param ProjectileLaunchEvent $event
     */
    public function onProjectileLaunch(ProjectileLaunchEvent $event): void {
        $entity = $event->getEntity();
        $areaManager = $this->core->getAreaManager();
        $areas = $areaManager->getAreasInPosition($entity->asPosition());
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getPvpFlag() === false) {
                    $event->setCancelled();
                    return;
                }
            }
        }
    }
}