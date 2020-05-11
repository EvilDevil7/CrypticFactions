<?php

declare(strict_types = 1);

namespace core\envoy;

use core\crate\Crate;
use core\envoy\task\EnvoyHeartbeatTask;
use core\item\ItemManager;
use core\item\types\ChestKit;
use core\item\types\CrateKeyNote;
use core\item\types\EnchantmentBook;
use core\item\types\MoneyNote;
use core\item\types\SacredStone;
use core\item\types\XPNote;
use core\Cryptic;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;

class EnvoyManager {

    /** @var Cryptic */
    private $core;

    /** @var Envoy[] */
    private $envoys = [];

    /** @var Reward[] */
    private $rewards = [];

    /**
     * EnvoyManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getScheduler()->scheduleRepeatingTask(new EnvoyHeartbeatTask($this), 20);
        $core->getServer()->getPluginManager()->registerEvents(new EnvoyListener($core), $core);
        $this->rewards = [
            new Reward("Pig Spawner", Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                new IntTag("EntityId", Entity::PIG)
            ]))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Pig Spawner"), 1),
            new Reward("Cow Spawner", Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                new IntTag("EntityId", Entity::COW)
            ]))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Cow Spawner"), 1),
            new Reward("Spider Spawner", Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                new IntTag("EntityId", Entity::SPIDER)
            ]))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Spider Spawner"), 1),
            new Reward("Blaze Spawner", Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                new IntTag("EntityId", Entity::BLAZE)
            ]))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Blaze Spawner"), 1),
            new Reward("Iron Golem Spawner", Item::get(Item::MOB_SPAWNER, 0, 1, new CompoundTag("", [
                new IntTag("EntityId", Entity::IRON_GOLEM)
            ]))->setCustomName(TextFormat::RESET . TextFormat::GOLD . "Iron Golem Spawner"), 1),
            new Reward("King Kit", (new ChestKit(Cryptic::getInstance()->getKitManager()->getKitByName("King")))->getItemForm(), 75),
            new Reward("Enchanted Golden Apples", Item::get(Item::ENCHANTED_GOLDEN_APPLE, 0, 8), 75),
            new Reward("Bedrock", Item::get(Item::BEDROCK, 0, 16), 50),
            new Reward("TNT", Item::get(Item::TNT, 0, 64), 100),
            new Reward("7,500 XP Note", (new XPNote(7500))->getItemForm(), 100),
            new Reward("15,000 XP Note", (new XPNote(15000))->getItemForm(), 75),
            new Reward("$10,000", (new MoneyNote(10000))->getItemForm(), 100),
            new Reward("$25,000", (new XPNote(25000))->getItemForm(), 75),
            new Reward("5 Common Crate Key Note", (new CrateKeyNote($core->getCrateManager()->getCrate(Crate::COMMON), 5))->getItemForm(), 100),
            new Reward("4 Legendary Crate Key Note", (new CrateKeyNote($core->getCrateManager()->getCrate(Crate::LEGENDARY), 4))->getItemForm(), 75),
            new Reward("3 Epic Crate Key Note", (new CrateKeyNote($core->getCrateManager()->getCrate(Crate::EPIC), 3))->getItemForm(), 100),
            new Reward("2 Tag Crate Key Note", (new CrateKeyNote($core->getCrateManager()->getCrate(Crate::TAG), 2))->getItemForm(), 75),
            new Reward("Enchantment", (new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm(), 75),
            new Reward("Sacred Stone", (new SacredStone())->getItemForm(), 1),
        ];
    }

    /**
     * @param Position $position
     */
    public function spawnEnvoy(Position $position): void {
        if(isset($this->envoys["$position"])) {
            $envoy = $this->envoys["$position"];
            if(!$envoy->isSpawned()) {
                $envoy->spawn();
            }
            return;
        }
        $this->envoys["$position"] = new Envoy($position);
    }

    /**
     * @param Position $position
     */
    public function despawnEnvoy(Position $position): void {
        if(!isset($this->envoys["$position"])) {
            return;
        }
        unset($this->envoys["$position"]);
    }

    /**
     * @return Envoy[]
     */
    public function getEnvoys(): array {
        return $this->envoys;
    }

    /**
     * @param Position $position
     *
     * @return Envoy|null
     */
    public function getEnvoy(Position $position): ?Envoy {
        return $this->envoys["$position"] ?? null;
    }

    /**
     * @return Reward[]
     */
    public function getRewards(): array {
        $amount = mt_rand(1, 4);
        $rewards = [];
        for($i = 1; $amount >= $i; $i++) {
            $rewards[] = $this->getReward();
        }
        return $rewards;
    }

    /**
     * @param int $loop
     *
     * @return Reward
     */
    private function getReward(int $loop = 0): Reward {
        $chance = mt_rand(0, 100);
        $reward = $this->rewards[array_rand($this->rewards)];
        if($loop >= 10) {
            return $reward;
        }
        if($reward->getChance() <= $chance) {
            return $this->getReward($loop + 1);
        }
        if($reward->getName() === "Enchantment") {
            $reward->setItem((new EnchantmentBook(ItemManager::getRandomEnchantment()))->getItemForm());
        }
        return $reward;
    }
}