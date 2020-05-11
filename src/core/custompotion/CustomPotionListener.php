<?php

declare(strict_types=1);

namespace core\custompotion;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;

class CustomPotionListener implements Listener{

    /**
     * @param PlayerItemConsumeEvent $event
     */
    public function onConsume(PlayerItemConsumeEvent $event) : void{
        $player = $event->getPlayer();
        if($event->getItem()->getId() === 373){
            $event->setCancelled();
            $damage = $event->getItem()->getDamage();
            switch($damage){
                case 100:
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::SPEED)))->setDuration(360 * 20)->setAmplifier(1));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::HASTE)))->setDuration(360 * 20)->setAmplifier(2));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION)))->setDuration(180 * 20)->setAmplifier(1));

                    $player->getInventory()->removeItem(Item::get(Item::POTION, 100, 1));
                    $player->getInventory()->addItem(Item::get(Item::GLASS_BOTTLE, 0, 1));
                    $player->addTitle("§l§8[§c+§8]§r §7Consumed:", "§l§cRaiding Elixir§r");
                    break;
                case 101:
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::JUMP)))->setDuration(180 * 20)->setAmplifier(1));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::STRENGTH)))->setDuration(30 * 20)->setAmplifier(1));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION)))->setDuration(360 * 20)->setAmplifier(1));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE)))->setDuration(360 * 20)->setAmplifier(1));
                    $player->getInventory()->removeItem(Item::get(Item::POTION, 101, 1));
                    $player->getInventory()->addItem(Item::get(Item::GLASS_BOTTLE, 0, 1));
                    $player->addTitle("§l§8[§b+§8]§r §7Consumed:", "§l§bPvP Elixir§r");
                    break;
                case 102:
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::REGENERATION)))->setDuration(360 * 20)->setAmplifier(2));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::ABSORPTION)))->setDuration(360 * 20)->setAmplifier(2));

                    $player->getInventory()->removeItem(Item::get(Item::POTION, 102, 1));
                    $player->getInventory()->addItem(Item::get(Item::GLASS_BOTTLE, 0, 1));
                    $player->addTitle("§l§8[§e+§8]§r §7Consumed:", "§l§eHealer Elixir§r");
                    break;
                case 103:
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::SPEED)))->setDuration(360 * 20)->setAmplifier(3));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::HASTE)))->setDuration(360 * 20)->setAmplifier(3));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE)))->setDuration(180 * 20)->setAmplifier(2));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::WATER_BREATHING)))->setDuration(180 * 20)->setAmplifier(2));
                    $player->addEffect((new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION)))->setDuration(180 * 20)->setAmplifier(2));

                    $player->getInventory()->removeItem(Item::get(Item::POTION, 103, 1));
                    $player->getInventory()->addItem(Item::get(Item::GLASS_BOTTLE, 0, 1));
                    $player->addTitle("§l§8[§d+§8]§r §7Consumed:", "§l§dMining Elixir§r");
                    break;
            }
        }
    }

    /**
     * @param PlayerItemHeldEvent $event
     */
    public function onHeld(PlayerItemHeldEvent $event) : void{
        $player = $event->getPlayer();
        if($event->getItem()->getId() === 373){
            $damage = $event->getItem()->getDamage();
            $hand = $player->getInventory()->getItemInHand();
            switch($damage){
                case 100:
                    $item = Item::get(Item::POTION, 100, 1);
                    $player->getInventory()->removeItem($item);
                    $item->setCustomName("§l§cRaiding Elixir§r");
                    $item->setLore([
                        "\n§8* §aSpeed I §7(6:00)\n§8* §aHaste II §7(6:00)\n§8* §aNight Vision §7(3:00)§r"
                    ]);
                    $player->getInventory()->addItem($item);
                    break;
                case 101:
                    $item = Item::get(Item::POTION, 101, 1);
                    $player->getInventory()->removeItem($item);
                    $item->setCustomName("§l§bPvP Elixir§r");
                    $item->setLore([
                        "\n§8* §aJump Boost I §7(3:00)\n§8* §aStrength I §7(0:30)\n§8* §aNight Vision §7(6:00)\n§8* §aFire Resistance §7(6:00)§r"
                    ]);
                    $player->getInventory()->addItem($item);
                    break;
                case 102:
                    $item = Item::get(Item::POTION, 102, 1);
                    $player->getInventory()->removeItem($item);
                    $item->setCustomName("§l§eHealer Elixir§r");
                    $item->setLore([
                        "\n§8* §aRegeneration II §7(3:00)\n§8* §aAbsorbrion II §7(3:00)§r"
                    ]);
                    $player->getInventory()->addItem($item);
                    break;
                case 103:
                    $item = Item::get(Item::POTION, 103, 1);
                    $player->getInventory()->removeItem($item);
                    $item->setCustomName("§l§dMining Elixir§r");
                    $item->setLore([
                        "\n§8* §aSpeed III §7(3:00)\n§8* §aHaste III §7(3:00)\n§8* §aFire Resistance II §7(3:00)\n§8* §aWater Breathing II §7(3:00)\n§8* §aNight Vision II §7(3:00)§r"
                    ]);
                    $player->getInventory()->addItem($item);
                    break;
            }
            if($hand->hasCustomName()){
                $event->setCancelled();

            }
        }
    }
}
