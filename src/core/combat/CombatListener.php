<?php

declare(strict_types = 1);

namespace core\combat;

use core\combat\boss\ArtificialIntelligence;
use core\item\types\Drops;
use core\Cryptic;
use core\CrypticPlayer;
use core\rank\Rank;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class CombatListener implements Listener {

    /** @var int[] */
    public $godAppleCooldown = [];

    /** @var int[] */
    public $goldenAppleCooldown = [];

    /** @var int[] */
    public $enderPearlCooldown = [];

    /** @var Cryptic */
    private $core;

    private const WHITELISTED = [
        "/gamemode",
        "/tp",
        "/ban",
        "/ban-ip",
        "/mute",
        "/kick",
        "/unban",
        "/freeze",
        "/tempban",
        "/tempblock",
        "/tempmute",
        "/staffmode"
    ];

    /**
     * CombatListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @priority NORMAL
     * @param PlayerCommandPreprocessEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerCommandPreprocess(PlayerCommandPreprocessEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if($player->getRank()->getIdentifier() >= Rank::TRAINEE and $player->getRank()->getIdentifier() <= Rank::OWNER) {
            return;
        }
        if(strpos($event->getMessage(), "/") !== 0) {
            return;
        }
        if(in_array(explode(" ", $event->getMessage())[0], self::WHITELISTED)) {
            return;
        }
        if($player->isTagged()) {
            $player->sendMessage(Translation::getMessage("noPermissionCombatTag"));
            $event->setCancelled();
        }
    }

    /**
     * @priority LOW
     * @param PlayerItemConsumeEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if($item->getId() === Item::ENCHANTED_GOLDEN_APPLE) {
            if(isset($this->godAppleCooldown[$player->getRawUniqueId()])) {
                if((time() - $this->godAppleCooldown[$player->getRawUniqueId()]) < 40) {
                    if(!$event->isCancelled()) {
                        $time = 40 - (time() - $this->godAppleCooldown[$player->getRawUniqueId()]);
                        $time = TextFormat::RED . $time . TextFormat::GRAY;
                        $player->sendMessage("§l§8(§c!§8)§r §7Slow down! This action is on cooldown for $time seconds!§r");
                        $event->setCancelled();
                        return;
                    }
                }
                $this->godAppleCooldown[$player->getRawUniqueId()] = time();
                return;
            }
            $this->godAppleCooldown[$player->getRawUniqueId()] = time();
            return;
        }
        if($item->getId() === Item::GOLDEN_APPLE) {
            if(isset($this->goldenAppleCooldown[$player->getRawUniqueId()])) {
                if((time() - $this->goldenAppleCooldown[$player->getRawUniqueId()]) < 2) {
                    if(!$event->isCancelled()) {
                        $time = 2 - (time() - $this->goldenAppleCooldown[$player->getRawUniqueId()]);
                        $time = TextFormat::RED . $time . TextFormat::GRAY;
                        $player->sendMessage("§l§8(§c!§8)§r §7Slow down! This action is on cooldown for $time seconds!§r");
                        $event->setCancelled();
                        return;
                    }
                }
                $this->goldenAppleCooldown[$player->getRawUniqueId()] = time();
                return;
            }
            $this->goldenAppleCooldown[$player->getRawUniqueId()] = time();
            return;
        }
    }

    /**
     * @priority NORMAL
     * @param PlayerRespawnEvent $event
     */
    public function onPlayerRespawn(PlayerRespawnEvent $event) {
        $player = $event->getPlayer();
        $level = $player->getServer()->getDefaultLevel();
        $spawn = $level->getSpawnLocation();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $this->core->getScheduler()->scheduleDelayedTask(new class($player, $spawn) extends Task {

            /** @var CrypticPlayer */
            private $player;

            /** @var Position */
            private $position;

            /**
             *  constructor.
             *
             * @param CrypticPlayer $player
             * @param Position      $position
             */
            public function __construct(CrypticPlayer $player, Position $position) {
                $this->player = $player;
                $this->position = $position;
            }

            /**
             * @param int $currentTick
             */
            public function onRun(int $currentTick) {
                if(!$this->player->isClosed()) {
                    $this->player->teleport($this->position);
                }
            }
        }, 1);
    }

    /**
     * @priority LOW
     * @param PlayerDeathEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $event->setDrops([(new Drops($player->getName(), $event->getDrops()))->getItemForm()]);
        $cause = $player->getLastDamageCause();
        $message = Translation::getMessage("death", [
            "name" => TextFormat::GREEN . $player->getName() . TextFormat::DARK_GRAY . "[" . TextFormat::DARK_RED . TextFormat::BOLD . $player->getKills() . TextFormat::RESET . TextFormat::DARK_GRAY . "]",
        ]);
        if($cause instanceof EntityDamageByEntityEvent) {
            $killer = $cause->getDamager();
            if($killer instanceof CrypticPlayer) {
                $killer->addKills();
                $message = Translation::getMessage("deathByPlayer", [
                    "name" => TextFormat::GREEN . $player->getName() . TextFormat::DARK_GRAY . "[" . TextFormat::DARK_RED . TextFormat::BOLD . $player->getKills() . TextFormat::RESET . TextFormat::DARK_GRAY . "]",
                    "killer" => TextFormat::RED . $killer->getName() . TextFormat::DARK_GRAY . "[" . TextFormat::DARK_RED . TextFormat::BOLD . $killer->getKills() . TextFormat::RESET . TextFormat::DARK_GRAY . "]"
                ]);
            }
        }
        $player->combatTag(false);
        $event->setDeathMessage($message);
    }

    /**
     * @priority NORMAL
     * @param PlayerMoveEvent $event
     *
     * @throws TranslationException
     */
    public function onPlayerMove(PlayerMoveEvent $event): void {
        $to = $event->getTo();
        $areas = $this->core->getAreaManager()->getAreasInPosition($to);
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if(!$player->isTagged()) {
            return;
        }
        if($areas === null) {
            return;
        }
        foreach($areas as $area) {
            if($area->getPvpFlag() === false) {
                $event->setCancelled();
                $player->sendMessage(Translation::getMessage("enterSafeZoneInCombat"));
                return;
            }
        }
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
        if($player->isTagged()) {
            $player->setHealth(0);
        }
    }

    /**
     * @priority HIGH
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $item = $event->getItem();
        if($item->getId() === Item::ENDER_PEARL) {
            if(!isset($this->enderPearlCooldown[$player->getRawUniqueId()])) {
                $this->enderPearlCooldown[$player->getRawUniqueId()] = time();
                return;
            }
            if(time() - $this->enderPearlCooldown[$player->getRawUniqueId()] < 10) {
                $event->setCancelled();
                return;
            }
            $this->enderPearlCooldown[$player->getRawUniqueId()] = time();
            return;
        }
    }

    /**
     * @priority HIGHEST
     * @param EntityDamageEvent $event
     *
     * @throws TranslationException
     */
    public function onEntityDamage(EntityDamageEvent $event): void {
        if($event->getCause() == $event::CAUSE_FALL and !$event->isCancelled()){
            $event->setCancelled();
        }
        if($event->isCancelled()) {
            return;
        }
        $entity = $event->getEntity();
        if($entity instanceof CrypticPlayer) {
            if($event->getCause() === EntityDamageEvent::CAUSE_FALL and ($entity->getLevel()->getFolderName() === "pvp" or $entity->getLevel()->getFolderName() === "bossarena")) {
                $event->setCancelled();
                return;
            }
            if($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if(!$damager instanceof CrypticPlayer) {
                    return;
                }
                if($damager->getLevel()->getFolderName() === "bossarena" and (!$entity instanceof ArtificialIntelligence)) {
                    $event->setCancelled();
                    return;
                }
                if($entity->isTagged()) {
                    $entity->combatTag();
                } else {
                    $entity->combatTag();
                    $entity->sendMessage(Translation::getMessage("combatTag"));
                }
                if($damager->isTagged()) {
                    $damager->combatTag();
                }
                else {
                    $damager->combatTag();
                    $damager->sendMessage(Translation::getMessage("combatTag"));
                }
                if($entity->isFlying() or $entity->getAllowFlight() and $entity->isSurvival()) {
                    $entity->setFlying(false);
                    $entity->setAllowFlight(false);
                    $entity->sendMessage(Translation::getMessage("flightToggle"));
                }
                if($damager->isFlying() or $damager->getAllowFlight() and $damager->isSurvival()) {
                    $damager->setFlying(false);
                    $damager->setAllowFlight(false);
                    $damager->sendMessage(Translation::getMessage("flightToggle"));
                }
            }
        }
    }

    /**
     * @priority HIGH
     * @param EntityTeleportEvent $event
     *
     * @throws TranslationException
     */
    public function onEntityTeleport(EntityTeleportEvent $event): void {
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        if(!$entity->isTagged()) {
            return;
        }
        $to = $event->getTo();
        if($to->getLevel() === null) {
            return;
        }
        $areas = $this->core->getAreaManager()->getAreasInPosition($to);
        if($areas === null) {
            return;
        }
        foreach($areas as $area) {
            if($area->getPvpFlag() === false) {
                $event->setCancelled();
                $entity->sendMessage(Translation::getMessage("enterSafeZoneInCombat"));
            }
        }
    }
}
