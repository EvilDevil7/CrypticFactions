<?php

declare(strict_types = 1);

namespace core\entity;

use core\command\forms\ChangeLogForm;
use core\command\forms\QuestMainForm;
use core\command\forms\ShopForm;
use core\entity\forms\AlchemistConfirmationForm;
use core\entity\forms\AlchemistConfirmationForm2;
use core\entity\npc\NPC;
use core\entity\npc\NPCListener;
use core\entity\types\Blaze;
use core\entity\types\Cow;
use core\entity\types\IronGolem;
use core\entity\types\Pig;
use core\entity\types\PrimedTNT;
use core\entity\types\Spider;
use core\item\CustomItem;
use core\Cryptic;
use core\entity\task\ExplosionQueueTask;
use core\CrypticPlayer;
use libs\utils\Utils;
use pocketmine\block\Bedrock;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockToolType;
use pocketmine\block\Obsidian;
use pocketmine\entity\Entity;
use pocketmine\entity\Explosive;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\item\TieredTool;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;

class EntityManager {

    const STACK_TAG = "Stack";

    const STACK_SIZE = "{SIZE}";

    const STACK_NAME = "{NAME}";

    /** @var Cryptic */
    private $core;

    /** @var ExplosionQueueTask */
    private $explosionQueue;

    /** @var NPC[] */
    private $npcs = [];

    /** @var string */
    private static $nametag;

    /**
     * EntityManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->explosionQueue = new ExplosionQueueTask();
        $core->getScheduler()->scheduleRepeatingTask($this->explosionQueue, 1);
        $core->getServer()->getPluginManager()->registerEvents(new EntityListener($core), $core);
        $core->getServer()->getPluginManager()->registerEvents(new NPCListener($core), $core);
        self::$nametag = TextFormat::DARK_GRAY . "(" . TextFormat::RED . self::STACK_SIZE . TextFormat::RESET . TextFormat::DARK_GRAY . ") " . TextFormat::RESET . TextFormat::DARK_RED . TextFormat::BOLD . self::STACK_NAME . TextFormat::RESET;
        $this->init();
    }

    public function init() {
        Entity::registerEntity(PrimedTNT::class, true);
        Entity::registerEntity(Blaze::class, true);
        Entity::registerEntity(Cow::class, true);
        Entity::registerEntity(IronGolem::class, true);
        Entity::registerEntity(Pig::class, true);
        Entity::registerEntity(Spider::class, true);
		BlockFactory::registerBlock(new class() extends Bedrock {

			/**
			 * @return float
			 */
			public function getBlastResistance() : float {
				return 36.41;
			}

			/**
			 * @return int
			 */
			public function getToolType() : int{
				return BlockToolType::TYPE_PICKAXE;
			}

			/**
			 * @return int
			 */
			public function getToolHarvestLevel() : int{
				return TieredTool::TIER_DIAMOND;
			}

			/**
			 * @return float
			 */
			public function getHardness() : float{
				return 999999;
			}

			/**
			 * @param Item $item
			 *
			 * @return Item[]
			 */
			public function getDropsForCompatibleTool(Item $item): array {
				return [
					Item::get(Item::BEDROCK, 0, 1)
				];
			}

			/**
			 * @param Item $item
			 *
			 * @return bool
			 */
			public function isBreakable(Item $item): bool {
				return true;
			}
		}, true);
		BlockFactory::registerBlock(new class() extends Obsidian {

			/**
			 * @return float
			 */
			public function getBlastResistance() : float {
				return 36.41;
			}
		}, true);
        $path = Cryptic::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "merchant.png";
        $this->addNPC(new NPC(Utils::createSkin(Utils::getSkinDataFromPNG($path)), new Position(263.5, 71, 253.5, $this->core->getServer()->getDefaultLevel()), TextFormat::BOLD . TextFormat::YELLOW . "Merchant\n" . TextFormat::GRAY . "(Tap me)", function(CrypticPlayer $player) {
            $player->sendForm(new ShopForm());
        }));
        $path = Cryptic::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "astronaut.png";
        $this->addNPC(new NPC(Utils::createSkin(Utils::getSkinDataFromPNG($path)), new Position(220.5, 71, 225.5, $this->core->getServer()->getDefaultLevel()), TextFormat::BOLD . TextFormat::LIGHT_PURPLE . "Adventurer\n" . TextFormat::GRAY . "(Tap me)", function(CrypticPlayer $player) {
            $player->sendForm(new QuestMainForm());
        }));
        $path = Cryptic::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "updater.png";
        $this->addNPC(new NPC(Utils::createSkin(Utils::getSkinDataFromPNG($path)), new Position(180.5, 71, 268.5, $this->core->getServer()->getDefaultLevel()), TextFormat::BOLD . TextFormat::GREEN . "Newspaper Boy\n" . TextFormat::GRAY . "(Tap me)", function(CrypticPlayer $player) {
            $player->sendForm(new ChangeLogForm());
        }));
        $path = Cryptic::getInstance()->getDataFolder() . "skins" . DIRECTORY_SEPARATOR . "alchemist.png";
        $this->addNPC(new NPC(Utils::createSkin(Utils::getSkinDataFromPNG($path)), new Position(178.5, 71, 312.5, $this->core->getServer()->getDefaultLevel()), TextFormat::BOLD . TextFormat::DARK_GREEN . "Alchemist\n" . TextFormat::GRAY . "(Tap me)", function(CrypticPlayer $player) {
            $item = $player->getInventory()->getItemInHand();
            if($item->hasEnchantments()) {
                foreach($player->getInventory()->getContents() as $i) {
                    $tag = $i->getNamedTagEntry(CustomItem::CUSTOM);
                    if($tag !== null and $i->getId() === Item::SUGAR) {
                        $player->sendForm(new AlchemistConfirmationForm2());
                        return;
                    }
                }
            }
            $tag = $item->getNamedTagEntry(CustomItem::CUSTOM);
            if($tag === null and $item->getId() !== Item::ENCHANTED_BOOK) {
                $player->sendMessage(TextFormat::DARK_GRAY . "[" . TextFormat::BOLD . TextFormat::DARK_GREEN . "Alchemist" . TextFormat::RESET . TextFormat::DARK_GRAY . "] " . TextFormat::WHITE . "That's not an enchantment book or an enchanted item! Get that #@%!%#* thing away from me!");
                return;
            }
            $player->sendForm(new AlchemistConfirmationForm($player));
        }));
    }

    /**
     * @return ExplosionQueueTask
     */
    public function getExplosionQueue(): ExplosionQueueTask {
        return $this->explosionQueue;
    }

    /**
     * @return NPC[]
     */
    public function getNPCs(): array {
        return $this->npcs;
    }

    /**
     * @param int $entityId
     *
     * @return NPC|null
     */
    public function getNPC(int $entityId): ?NPC {
        return $this->npcs[$entityId] ?? null;
    }

    /**
     * @param NPC $npc
     */
    public function addNPC(NPC $npc): void {
        $this->npcs[$npc->getEntityId()] = $npc;
    }

    /**
     * @param NPC $npc
     */
    public function removeNPC(NPC $npc): void {
        unset($this->npcs[$npc->getEntityId()]);
    }

    /**
     * @param Entity $entity
     *
     * @return bool
     */
    public static function canStack(Entity $entity): bool {
        return $entity instanceof Living and (!$entity instanceof Human) and (!$entity instanceof Explosive);
    }

    /**
     * @param Living $entity
     */
    public static function addToStack(Living $entity) {
        $bb = $entity->getBoundingBox()->expandedCopy(16, 16, 16);
        foreach($entity->getLevel()->getNearbyEntities($bb) as $e) {
            if($e->namedtag->hasTag(self::STACK_TAG) and $e instanceof Living and $e->getName() === $entity->getName()) {
                $entity->flagForDespawn();
                self::increaseStackSize($e);
                return;
            }
        }
        self::setStackSize($entity);
    }

    /**
     * @param Living $entity
     * @param int $size
     *
     * @return bool
     */
    public static function setStackSize(Living $entity, int $size = 1): bool {
        $entity->namedtag->setInt(self::STACK_TAG, $size);
        if($size < 1) {
            $entity->flagForDespawn();
            return false;
        }
        self::updateEntityName($entity);
        return true;
    }

    /**
     * @param Living $entity
     * @param int $size
     */
    public static function increaseStackSize(Living $entity, int $size = 1) {
        if($entity->namedtag !== null) {
            self::setStackSize($entity, $entity->namedtag->getInt(self::STACK_TAG, 0) + $size);
        }
    }

    /**
     * @param Living $entity
     * @param int $size
     */
    public static function decreaseStackSize(Living $entity, int $size = 1) {
        if($size > 0) {
            $currentSize = $entity->namedtag->getInt(self::STACK_TAG);
            $decr = min($size, $currentSize);
            $newSize = $currentSize - $decr;
            $level = $entity->getLevel();
            if(self::setStackSize($entity, $newSize)) {
                $entity->setHealth($entity->getMaxHealth());
            }
            for($i = 0; $i < $decr; ++$i) {
                foreach($entity->getDrops() as $item) {
                    $level->dropItem($entity, $item);
                }
            }
        }
    }

    /**
     * @param Living $entity
     */
    public static function updateEntityName(Living $entity): void {
        $entity->setNameTag(
            strtr(
                self::$nametag, [
                self::STACK_SIZE => $entity->namedtag->getInt(self::STACK_TAG),
                self::STACK_NAME => strtoupper($entity->getName())
            ])
        );
    }
}
