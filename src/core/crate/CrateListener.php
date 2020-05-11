<?php

declare(strict_types = 1);

namespace core\crate;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\TranslationException;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;

class CrateListener implements Listener {

    /** @var Cryptic */
    private $core;

    /**
     * CrateListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        foreach($this->core->getCrateManager()->getCrates() as $crate) {
            $crate->spawnTo($player);
        }
    }

    /**
     * @priority LOWEST
     * @param PlayerInteractEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $block = $event->getBlock();
        foreach($this->core->getCrateManager()->getCrates() as $crate) {
            if($crate->getPosition()->equals($block->asPosition())) {
                $crate->try($player);
                $event->setCancelled();
            }
        }
    }
}