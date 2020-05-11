<?php

declare(strict_types = 1);

namespace core\item\enchantment\types;

use core\item\enchantment\Enchantment;
use core\item\types\Head;
use core\CrypticPlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class GuillotineEnchantment extends Enchantment {

    /**
     * GuillotineEnchantment constructor.
     */
    public function __construct() {
        parent::__construct(self::GUILLOTINE, "Guillotine", self::RARITY_MYTHIC, "Have a chance to obtain your opponent's head.", self::DAMAGE, self::SLOT_SWORD, 10);
        $this->callable = function(EntityDamageByEntityEvent $event, int $level) {
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            if((!$damager instanceof CrypticPlayer) or (!$entity instanceof CrypticPlayer)) {
                return;
            }
            if($event->getFinalDamage() < $entity->getHealth()) {
                return;
            }
            $random = mt_rand(1, 10);
            if($level >= $random) {
                $head = Item::get(Item::SKULL, mt_rand(50, 100), 1);
                $head->setCustomName(TextFormat::AQUA . $entity->getNameTag() . "' head");
                $head->setLore([TextFormat::GRAY . "sell it!"]);
                $nbt = $head->getNamedTag();
                $nbt->setString("head", $entity->getNameTag());
                $nbt->setString("type", "human");
                $head->setNamedTag($nbt);
                $damager->getInventory()->addItem($head);
            }
        };
    }
}