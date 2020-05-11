<?php

declare(strict_types = 1);

namespace core;

use core\announcement\AnnouncementManager;
use core\area\AreaException;
use core\area\AreaManager;
use core\clearlag\ClearLagManager;
use core\combat\boss\BossException;
use core\combat\boss\tasks\SpawnWitcherBoss;
use core\combat\CombatManager;
use core\command\CommandManager;
use core\command\task\CheckVoteTask;
use core\crate\CrateManager;
use core\custompotion\CustomPotionListener;
use core\entity\EntityManager;
use core\envoy\EnvoyManager;
use core\event\EventManager;
use core\faction\FactionManager;
use core\gamble\GambleManager;
use core\item\ItemManager;
use core\kit\KitException;
use core\level\LevelManager;
use core\mask\MaskManager;
use core\price\PriceManager;
use core\provider\MySQLProvider;
use core\quest\QuestException;
use core\quest\QuestManager;
use core\rank\RankException;
use core\rank\RankManager;
use core\tag\TagManager;
use core\trade\TradeManager;
use core\update\UpdateManager;
use core\watchdog\WatchdogManager;
use core\kit\KitManager;
use core\libs\muqsit\invmenu\InvMenuHandler;
use Exception;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use pocketmine\utils\Internet;
use ReflectionException;

class Cryptic extends PluginBase {

    static $debug = true;

    private static $STARTUP_WORLDS = ["wild", "pvp", "bossarena"];
    private static $DIRECTORIES = ["factions", "players", "kits"];
    private static $SERVER_NAME = "§l§6Cryptic§ePE§r §7- §l§b Op Factions§r§7";

    const MESSAGES = [
        "§l§6Cryptic§ePE§r §7- §l§bSERVER RESET!§r§7",
        "§l§6Cryptic§ePE§r §7- §l§bSERVER RELEASED!§r§7",
        "§l§6Cryptic§ePE§r §7- §l§bSEASON I§r§7",
    ];

    const BORDER = 20000;

    const WORLD = "260";

    const EXTRA_SLOTS = 20;

    /** @var self */
    private static $instance;

    /** @var BigEndianNBTStream */
    private static $nbtWriter;

    /** @var AreaManager */
    private $areaManager;

    /** @var AnnouncementManager */
    private $announcementManager;

    /** @var CommandManager */
    private $commandManager;

    /** @var WatchdogManager */
    private $watchdogManager;

    /** @var RankManager */
    private $rankManager;

    /** @var FactionManager */
    private $factionManager;

    /** @var EntityManager */
    private $entityManager;

    /** @var CombatManager */
    private $combatManager;

	/*** @var KitManager */
    private $kitManager;

    /** @var LevelManager */
    private $levelManager;

    /** @var UpdateManager */
    private $updateManager;

    /** @var ItemManager */
    private $itemManager;

    /** @var CrateManager */
    private $crateManager;

    /** @var PriceManager */
    private $priceManager;

    /** @var EnvoyManager */
    private $envoyManager;

    /** @var QuestManager */
    private $questManager;

    /** @var TradeManager */
    private $tradeManager;

    /** @var GambleManager */
    private $gambleManager;

    /** @var EventManager */
    private $eventManager;

    /** @var MaskManager */
    private $mask;

    /** @var ClearLagManager */
    private $clearlag;

    /** @var TagManager */
    private $tagManager;

    /** @var MySQLProvider $provider */
    private $provider;

    /** @var int */
    private $votes = 0;

    /** @var array */
    private $rewards = [];

    /** @var array */
    private $inbox = [];

    /** @var array */
    private $sessions = [];

    /**
     * @return void
     */
    function onLoad(): void {
        self::$nbtWriter = new BigEndianNBTStream();
        self::$instance = $this;
        $this->getServer()->getNetwork()->setName(self::$SERVER_NAME);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    static function log(string $message): void {
        self::$instance->getLogger()->info($message);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    static function debug(string $message): void {
        if(self::$debug)self::log("[DEBUG] ".$message);
    }

    /**
     * @return void
     */
    function onEnable() {
        if(!InvMenuHandler::isRegistered())InvMenuHandler::register($this);
        $this->initFolders();
        $this->saveResources();
        $this->loadWorlds();
        $this->initVariables();

        $this->initCommands();
        $this->initEvents();
        $this->initTasks();

        $get = Internet::getURL(CheckVoteTask::STATS_URL);
        if($get !== false) {
            $get = json_decode($get, true);
            if(isset($get["votes"])) {
                $this->votes = (int)$get["votes"];
            }
        }
    }

    /**
     * @return void
     */
    function initCommands(): void {

    }

    /**
     * @return void
     */
    function initEvents(): void {
        $this->getServer()->getPluginManager()->registerEvents(new CrypticListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new CustomPotionListener(), $this);
    }

    /**
     * @return void
     */
    function initTasks(): void {
        $this->getScheduler()->scheduleRepeatingTask(new SpawnWitcherBoss(), 20);
    }

    /**
     * @return void
     */
    function onDisable(): void{
        $this->getKitManager()->save();
        #TODO: Close all sessions
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    static function encodeItem(Item $item): string {
        return self::$nbtWriter->writeCompressed($item->nbtSerialize());
    }

    /**
     * @param string $compression
     *
     * @return Item
     */
    static function decodeItem(string $compression): Item {
        $tag = self::$nbtWriter->readCompressed($compression);
        if(!$tag instanceof CompoundTag)throw new PluginException("Expected a CompoundTag, got " . get_class($tag));

        return Item::nbtDeserialize($tag);
    }

    /**
     * @param Inventory $inventory
     * @param array     $items
     *
     * @return string
     */
    static function encodeInventory(Inventory $inventory, $items = []): string {
        foreach($inventory->getContents() as $item) {
            $items = $item->nbtSerialize();
        }

        return self::$nbtWriter->writeCompressed(new CompoundTag("Content", [new ListTag("Items", $items)]));
    }

    /**
     * @param string $compression
     *
     * @return array
     */
    static function decodeInventory(string $compression): array {
        if(empty($compression)) {
            return [];
        }

        $tag = self::$nbtWriter->readCompressed($compression);
        if(!$tag instanceof CompoundTag)throw new PluginException("Expected a CompoundTag, got " . get_class($tag));

        $content = [];
        /** @var CompoundTag $item */
        foreach($tag->getListTag("Items")->getValue() as $item) {
            $content[] = Item::nbtDeserialize($item);
        }
        return $content;
    }

    /**
     * @return void
     */
    function initFolders(): void {
        foreach (self::$DIRECTORIES as $DIRECTORY) {
            @mkdir($this->getDataFolder().$DIRECTORY);
        }
        self::debug("Successfully initiated all directories.");
    }

    /**
     * @return void
     */
    function saveResources(): void {
        $this->saveResource("rules.txt");
        $this->saveResource("alias.json");
        self::debug("Successfully saved all resources.");
    }

    /**
     * @return void
     */
    function loadWorlds(): void {
        foreach (self::$STARTUP_WORLDS as $STARTUP_WORLD) {
            $this->getServer()->loadLevel($STARTUP_WORLD);
        }
        self::debug("Successfully loaded all default worlds.");
    }

    /**
     * @return void
     */
	function initVariables(): void {
        try {
            $this->provider = new MySQLProvider($this);
            $this->areaManager = new AreaManager($this);
            $this->announcementManager = new AnnouncementManager($this);
            $this->watchdogManager = new WatchdogManager($this);
            $this->rankManager = new RankManager($this);
            $this->factionManager = new FactionManager($this);
            $this->entityManager = new EntityManager($this);
            $this->combatManager = new CombatManager($this);
            $this->levelManager = new LevelManager($this);
            $this->updateManager = new UpdateManager($this);
            $this->itemManager = new ItemManager($this);
            $this->kitManager = new KitManager($this);
            $this->tagManager = new TagManager();
            $this->crateManager = new CrateManager($this);
            $this->priceManager = new PriceManager($this);
            $this->commandManager = new CommandManager($this);
            $this->envoyManager = new EnvoyManager($this);
            $this->questManager = new QuestManager($this);
            $this->tradeManager = new TradeManager($this);
            $this->gambleManager = new GambleManager($this);
            $this->eventManager = new EventManager($this);
            $this->mask = new MaskManager();
            $this->clearlag = new ClearLagManager();
            self::debug("Successfully loaded all server managers.");
        }catch (Exception $exception) {
            self::debug("There was an unexpected error while attempting to initiate \"Cryptic.php\"'s variables.");
        }
    }

    /**
     * @return int
     */
    function getVotes(): int {
        return $this->votes;
    }

    /**
     * @param int $amount
     */
    function setVotes(int $amount): void {
        $this->votes = $amount;
    }

    /**
     * @return Cryptic
     */
    static function getInstance(): Cryptic {
        return self::$instance;
    }

    /**
     * @return MySQLProvider
     */
    function getMySQLProvider(): MySQLProvider {
        return $this->provider;
    }

    /**
     * @return AreaManager
     */
    function getAreaManager(): AreaManager {
        return $this->areaManager;
    }

    /**
     * @return AnnouncementManager
     */
    function getAnnouncementManager(): AnnouncementManager {
        return $this->announcementManager;
    }

    /**
     * @return CommandManager
     */
    function getCommandManager(): CommandManager {
        return $this->commandManager;
    }

    /**
     * @return WatchdogManager
     */
    function getWatchdogManager(): WatchdogManager {
        return $this->watchdogManager;
    }

    /**
     * @return RankManager
     */
    function getRankManager(): RankManager {
        return $this->rankManager;
    }

    /**
     * @return FactionManager
     */
    function getFactionManager(): FactionManager {
        return $this->factionManager;
    }

    /**
     * @return EntityManager
     */
    function getEntityManager(): EntityManager {
        return $this->entityManager;
    }

    /**
     * @return CombatManager
     */
    function getCombatManager(): CombatManager {
        return $this->combatManager;
    }

    /**
     * @return KitManager
     */
    function getKitManager(): KitManager {
        return $this->kitManager;
    }

    /**
     * @return LevelManager
     */
    function getLevelManager(): LevelManager {
        return $this->levelManager;
    }

    /**
     * @return UpdateManager
     */
    function getUpdateManager(): UpdateManager {
        return $this->updateManager;
    }

    /**
     * @return ItemManager
     */
    function getItemManager(): ItemManager {
        return $this->itemManager;
    }

    /**
     * @return CrateManager
     */
    function getCrateManager(): CrateManager {
        return $this->crateManager;
    }

    /**
     * @return PriceManager
     */
    function getPriceManager(): PriceManager {
        return $this->priceManager;
    }

    /**
     * @return EnvoyManager
     */
    function getEnvoyManager(): EnvoyManager {
        return $this->envoyManager;
    }

    /**
     * @return QuestManager
     */
    function getQuestManager(): QuestManager {
        return $this->questManager;
    }

    /**
     * @return TradeManager
     */
    function getTradeManager(): TradeManager {
        return $this->tradeManager;
    }

    /**
     * @return GambleManager
     */
    function getGambleManager(): GambleManager {
        return $this->gambleManager;
    }

    /**
     * @return EventManager
     */
    function getEventManager(): EventManager {
        return $this->eventManager;
    }

    /**
     * @return MaskManager
     */
    function getMaskManager(): MaskManager{
        return $this->mask;
    }

    /**
     * @return ClearLagManager
     */
    function getClearlag(): ClearLagManager{
        return $this->clearlag;
    }

    /**
     * @return TagManager
     */
    function getTagManager(): TagManager{
        return $this->tagManager;
    }
}
