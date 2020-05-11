<?php

declare(strict_types = 1);

namespace core\item\enchantment;

use core\item\CustomItem;
use core\item\ItemManager;
use core\item\types\EnchantmentBook;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\level\sound\AnvilUseSound;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class EnchantmentListener implements Listener {

    /** @var Cryptic */
    private $core;

    /**
     * EnchantmentListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @priority HIGHEST
     * @param EntityDamageEvent $event
     */
    public function onEntityDamage(EntityDamageEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        if($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if(!$damager instanceof CrypticPlayer) {
                return;
            }
            $enchantments = $damager->getActiveEnchantments();
            if(!isset($enchantments[Enchantment::DAMAGE])) {
                return;
            }
            /** @var EnchantmentInstance $enchantment */
            foreach($enchantments[Enchantment::DAMAGE] as $enchantment) {
                /** @var Enchantment $type */
                $type = $enchantment->getType();
                $callable = $type->getCallable();
                $callable($event, $enchantment->getLevel());
            }
            $enchantments = $entity->getActiveEnchantments();
            if(!isset($enchantments[Enchantment::DAMAGE_BY])) {
                return;
            }
            /** @var EnchantmentInstance $enchantment */
            foreach($enchantments[Enchantment::DAMAGE_BY] as $enchantment) {
                /** @var Enchantment $type */
                $type = $enchantment->getType();
                $callable = $type->getCallable();
                $callable($event, $enchantment->getLevel());
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param EntityEffectAddEvent $event
     */
    public function onEntityEffectAdd(EntityEffectAddEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        if($event->isCancelled()) {
            return;
        }
        $enchantments = $entity->getActiveEnchantments();
        if(!isset($enchantments[Enchantment::EFFECT_ADD])) {
            return;
        }
        /** @var EnchantmentInstance $enchantment */
        foreach($enchantments[Enchantment::EFFECT_ADD] as $enchantment) {
            /** @var Enchantment $type */
            $type = $enchantment->getType();
            $callable = $type->getCallable();
            $callable($event, $enchantment->getLevel());
        }
    }

    /**
     * @priority HIGHEST
     * @param EntityShootBowEvent $event
     */
    public function onEntityShootBow(EntityShootBowEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $entity = $event->getEntity();
        if(!$entity instanceof CrypticPlayer) {
            return;
        }
        $enchantments = $entity->getActiveEnchantments();
        if(!isset($enchantments[Enchantment::SHOOT])) {
            return;
        }
        /** @var EnchantmentInstance $enchantment */
        foreach($enchantments[Enchantment::SHOOT] as $enchantment) {
            /** @var Enchantment $type */
            $type = $enchantment->getType();
            $callable = $type->getCallable();
            $callable($event, $enchantment->getLevel());
        }
    }

    /**
     * @priority HIGH
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event) {
        $cause = $event->getEntity()->getLastDamageCause();
        if($cause instanceof EntityDamageByEntityEvent) {
            $player = $event->getPlayer();
            if(!$player instanceof CrypticPlayer) {
                return;
            }
            $enchantments = $player->getActiveEnchantments();
            if(!isset($enchantments[Enchantment::DEATH])) {
                return;
            }
            /** @var EnchantmentInstance $enchantment */
            foreach($enchantments[Enchantment::DEATH] as $enchantment) {
                /** @var Enchantment $type */
                $type = $enchantment->getType();
                $callable = $type->getCallable();
                $callable($event, $enchantment->getLevel());
            }
        }
    }

    /**
     * @priority HIGHEST
     * @param PlayerMoveEvent $event
     */
    public function onPlayerMove(PlayerMoveEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $enchantments = $player->getActiveEnchantments();
        if(!isset($enchantments[Enchantment::MOVE])) {
            return;
        }
        /** @var EnchantmentInstance $enchantment */
        foreach($enchantments[Enchantment::MOVE] as $enchantment) {
            /** @var Enchantment $type */
            $type = $enchantment->getType();
            $callable = $type->getCallable();
            $callable($event, $enchantment->getLevel());
        }
    }

    /**
     * @priority HIGHEST
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
        $enchantments = $player->getActiveEnchantments();
        if(!isset($enchantments[Enchantment::INTERACT])) {
            return;
        }
        /** @var EnchantmentInstance $enchantment */
        foreach($enchantments[Enchantment::INTERACT] as $enchantment) {
            /** @var Enchantment $type */
            $type = $enchantment->getType();
            $callable = $type->getCallable();
            $callable($event, $enchantment->getLevel());
        }
    }

    /**
     * @priority HIGHEST
     * @param BlockBreakEvent $event
     */
    public function onBlockBreak(BlockBreakEvent $event) {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $enchantments = $player->getActiveEnchantments();
        if(!isset($enchantments[Enchantment::BREAK])) {
            return;
        }
        /** @var EnchantmentInstance $enchantment */
        foreach($enchantments[Enchantment::BREAK] as $enchantment) {
            /** @var Enchantment $type */
            $type = $enchantment->getType();
            $callable = $type->getCallable();
            $callable($event, $enchantment->getLevel());
        }
    }

    /**
     * @priority LOWEST
     * @param InventoryTransactionEvent $event
     *
     * @throws TranslationException
     */
    public function onInventoryTransaction(InventoryTransactionEvent $event) {
        $transaction = $event->getTransaction();
        foreach($transaction->getActions() as $action) {
            if($action instanceof SlotChangeAction) {
                $sourceItem = $action->getSourceItem();
                if($sourceItem->getId() === Item::ENCHANTED_BOOK) {
                    $tag = $sourceItem->getNamedTagEntry(CustomItem::CUSTOM);
                    if($tag instanceof CompoundTag) {
                        $enchantmentBookAction = $action;
                        $enchantment = Enchantment::getEnchantment($tag->getInt(EnchantmentBook::ENCHANTMENT));
                    }
                }
                elseif(!$sourceItem->isNull()) {
                    $equipmentAction = $action;
                }
            }
        }
        $player = $transaction->getSource();
        if(isset($enchantmentBookAction, $equipmentAction, $enchantment)) {
            $book = $enchantmentBookAction->getSourceItem();
            $equipment = $equipmentAction->getSourceItem();
            if(ItemManager::canEnchant($equipment, $enchantment)) {
                $enchantment = new EnchantmentInstance($enchantment);
                if($equipment->hasEnchantment($enchantment->getId())) {
                    $enchantment->setLevel($equipment->getEnchantmentLevel($enchantment->getId()) + 1);
                    $levels = round(10 + (($enchantment->getLevel() * 5) * ItemManager::rarityToMultiplier($enchantment->getType()->getRarity())));
                }
                else {
                    $enchantment->setLevel(1);
                    $levels = round(20 * ItemManager::rarityToMultiplier($enchantment->getType()->getRarity()));
                }
                if($player->getXpLevel() < $levels) {
                    $player->sendMessage(Translation::getMessage("needLevelsToEnchant", [
                        "amount" => TextFormat::RED . $levels
                    ]));
                    return;
                }
                $player->subtractXpLevels((int)$levels);
                $equipmentAction->getInventory()->removeItem($equipment);
                $enchantmentBookAction->getInventory()->removeItem($book);
                $equipment->addEnchantment($enchantment);
                $lore = [];
                foreach($equipment->getEnchantments() as $enchantment) {
                    if($enchantment->getType() instanceof Enchantment) {
                        $lore[] = TextFormat::RESET . ItemManager::rarityToColor($enchantment->getType()->getRarity()) . $enchantment->getType()->getName() . " " . ItemManager::getRomanNumber($enchantment->getLevel());
                    }
                }
                $equipment->setLore($lore);
                $equipmentAction->getInventory()->addItem($equipment);
                $event->setCancelled();
                $player->getLevel()->addSound(new AnvilUseSound($player));
            }
        }
    }
}