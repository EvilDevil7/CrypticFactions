<?php

declare(strict_types = 1);

namespace core\mask;

use core\Cryptic;
use core\CrypticPlayer;
use libs\utils\Utils;
use pocketmine\entity\Effect;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityArmorChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class MaskListener implements Listener{

    /**
     * @param BlockPlaceEvent $e
     */
    public function onPlace(BlockPlaceEvent $e): void{
        if($e->getBlock()->getId() == Item::SKULL_BLOCK or $e->getBlock()->getId() == Item::SKULL){
            $e->setCancelled();
        }
    }

    /**
     * @param EntityDamageEvent $e
     */
    public function onEntityDamage(EntityDamageEvent $e): void{
        $entity = $e->getEntity();

        if($e instanceof EntityDamageByEntityEvent){
            $damager = $e->getDamager();

            if($entity instanceof Player and $damager instanceof Player){
                $mask = Cryptic::getInstance()->getMaskManager()->getMasks()[10];
                if($mask->hasMask($entity) and $entity->getHealth() <= 7){
                    Utils::addEffect($entity, Effect::REGENERATION, 10, 3);
                    Utils::addEffect($entity, Effect::SPEED, 10, 2);
                }
            }
        }
    }

    /**
     * @param PlayerKickEvent $e
     */
    public function onFlyKick(PlayerKickEvent $e): void{
        $player = $e->getPlayer();

        if($e->getReason() == "Flying is not enabled on this server"){
            $mask = Cryptic::getInstance()->getMaskManager()->getMasks()[5];
            if($mask->hasMask($player)) $e->setCancelled();
        }
    }

    /**
     * @param EntityArmorChangeEvent $e
     */
    public function onEquipDragonMask(EntityArmorChangeEvent $e): void{
        $item = $e->getNewItem();
        $old = $e->getOldItem();
        $player = $e->getEntity();

        if(!$player instanceof CrypticPlayer){
            return;
        }
        if($item->getId() == Item::SKULL){
            if($item->getDamage() == 5){
                if(!$player->isTagged()){
                    $player->setAllowFlight(true);
                    $player->setFlying(true);
                }else{
                    if($player->getAllowFlight() or $player->isFlying()){
                        $player->setAllowFlight(false);
                        $player->setFlying(false);
                    }
                }
            }
        }

        if($old->getId() == Item::SKULL){
            if($old->getDamage() == 5){
                $player->setAllowFlight(false);
                $player->setFlying(false);
            }
        }
    }

    /**
     * @param PlayerInteractEvent $e
     */
    public function onCustomMask(PlayerInteractEvent $e): void{
        $player = $e->getPlayer();
        $item = $e->getItem();
        $ic = clone $item;

        if($item->getId() == Item::ENCHANTED_BOOK){
            if($item->getDamage() == 101){
                $damage = mt_rand(3, 10);
                $mask = Cryptic::getInstance()->getMaskManager()->getMasks()[$damage];
                $item = Item::get(Item::SKULL, $damage, 1);
                $item->setCustomName(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . $mask->getName());
                $item->setLore($mask->getLore());
                $ic->setCount(1);
                $player->getInventory()->removeItem($ic);
                $player->getInventory()->addItem($item);
                $player->addTitle(TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Obtained ", TextFormat::GRAY . $mask->getName());
            }
        }
    }
}
