<?php

declare(strict_types = 1);

namespace core\level;

use core\item\types\HolyBox;
use core\item\types\LuckyBlock;
use core\item\types\SacredStone;
use core\level\tile\Generator;
use core\level\tile\MobSpawner;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\utils\UtilsException;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\inventory\FurnaceSmeltEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LevelListener implements Listener {

    /** @var Cryptic */
    private $core;

    /**
     * LevelListener constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @param PlayerJoinEvent $event
     *
     * @throws UtilsException
     */
    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $level = $this->core->getServer()->getDefaultLevel();
        $player->addFloatingText(new Position(241.28, 73.22, 305.86, $level), "Info", "§7Welcome to §l§6Cryptic§ePE§r §7-§r §l§bSeason I§r\n \n§7You are currently playing on our OP Factions server. \n \nUse Once Kit to start\nyour amazing adventures!\n \nCheck changes by /changelog!§r");
        $player->addFloatingText(new Position(10163, 104, 9988.5, $this->core->getServer()->getLevelByName("pvp")), "PVP", TextFormat::RED . TextFormat::BOLD . "(!) PVP is enabled below! (!)§r");
    }

    /**
     * @priority HIGHEST
     * @param PlayerInteractEvent $event
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $block = $event->getBlock();
        $tile = $block->getLevel()->getTile($block);
        $item = $event->getItem();
        if($item->getNamedTag()->hasTag("EntityId")) {
            $entityId = $item->getNamedTag()->getInt("EntityId", -1);
            if($entityId < 10) {
                return;
            }
            if($tile instanceof MobSpawner and $tile->getEntityId() === $entityId and $tile->getStack() < 50) {
                $stack = $tile->getStack() + 1;
                $tile->setStack($stack);
                $player->sendMessage("§l§8(§a!§8)§r §7STACKED: " . $stack . "/50");
                $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
                $event->setCancelled();
            }
        }
        if($tile instanceof Generator and $block->getItemId() === $item->getId() and $tile->getStack() < 25) {
            $stack = $tile->getStack() + 1;
            $tile->setStack($stack);
            $player->sendMessage("§l§8(§a!§8)§r §7STACKED: " . $stack . "/25");
            $player->getInventory()->setItemInHand($item->setCount($item->getCount() - 1));
            $event->setCancelled();
        }
    }

    /**
     * @priority HIGHEST
     * @param BlockBreakEvent $event
     *
     * @throws TranslationException
     */
    public function onBlockBreak(BlockBreakEvent $event): void {
        if($event->isCancelled()) {
            return;
        }
        $player = $event->getPlayer();
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        $block = $event->getBlock();
        if($block->getId() === Block::STONE) {
            if(mt_rand(1, 150) === mt_rand(1, 150)) {
                $item = new LuckyBlock(mt_rand(0, 100));
                if(!$player->getInventory()->canAddItem($item->getItemForm())){
                    $player->addReward($item->getItemForm());
                    $player->sendMessage("§l§8(§c!§8)§r §7Your inventory is full, therefore we are putting your item into /rewards. Check /rewards to get your item.§r");
                    return;
                }
                $player->getInventory()->addItem($item->getItemForm());
                $player->sendMessage(Translation::getMessage("luckyBlockFound"));
                $player->addLuckyBlocksMined();
            }
            if(mt_rand(1, 7500) === mt_rand(1, 7500)) {
                $item = new SacredStone();
                if(!$player->getInventory()->canAddItem($item->getItemForm())){
                    $player->addReward($item->getItemForm());
                    $player->sendMessage("§l§8(§c!§8)§r §7Your inventory is full, therefore we are putting your item into /rewards. Check /rewards to get your item.§r");
                    return;
                }
                $player->getInventory()->addItem($item->getItemForm());
                Server::getInstance()->broadcastMessage(Translation::PURPLE . $player->getDisplayName() . TextFormat::YELLOW . " discovered a sacred stone while mining!§r");
            }
        }
    }

    /**
     * @priority LOWEST
     * @param FurnaceSmeltEvent $event
     */
    public function onFurnaceSmelt(FurnaceSmeltEvent $event): void {
        $id = $event->getResult()->getId();
        if($id >= Block::PURPLE_GLAZED_TERRACOTTA and $id <= Block::BLACK_GLAZED_TERRACOTTA) {
            $event->setCancelled();
        }
    }
}
