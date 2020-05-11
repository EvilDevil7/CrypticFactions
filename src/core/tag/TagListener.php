<?php

declare(strict_types = 1);

namespace core\tag;


use core\CrypticPlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\nbt\tag\StringTag;
use pocketmine\level\sound\BlazeShootSound;

class TagListener implements Listener{

    /** @var TagManager */
    private $tag;

    /**
     * TagListener constructor.
     * @param TagManager $tagManager
     */
    public function __construct(TagManager $tagManager){
        $this->tag = $tagManager;
    }

    public function onInteract(PlayerInteractEvent $e): void{
        /** @var CrypticPlayer $p */
        $p = $e->getPlayer();
        $inv = $p->getInventory();
        $nbt = $e->getItem()->getNamedTag();

        if($nbt->hasTag("tag", StringTag::class)){
            $tags = $this->tag->getTagList($p);
            if($tags == null){
                $tags = [];
            }
            if(in_array($nbt->getString("tag"), $tags)){
                $p->sendMessage("§l§8(§c!§8)§r §7You own this tag, already! Therefore, you cannot claim this tag.§r");
                return;
            }
            $this->tag->giveTag($p, $nbt->getString("tag"));
            $item = $inv->getItemInHand();
            $item->count--;
            $inv->remove($item);
            $p->sendMessage("§l§8(§a!§8)§r §7You've sucessfully claimed this tag.\n§l§8(§a!§8)§r §7To enable this tag, type the command, §a/tags§7.§r");
            $p->playXpLevelUpSound();
	    $p->getLevel()->addSound(new BlazeShootSound($p));
        }
    }
}
