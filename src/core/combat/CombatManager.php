<?php

declare(strict_types = 1);

namespace core\combat;

use core\combat\boss\Boss;
use core\combat\boss\BossException;
use core\combat\boss\types\Alien;
use core\combat\boss\types\CorruptedKing;
use core\combat\boss\types\Witcher;
use core\Cryptic;
use core\CrypticPlayer;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class CombatManager {

    /** @var Cryptic */
    private $core;

    /** @var CombatListener */
    private $listener;

    /** @var string[] */
    private $bosses = [];

    /** @var Boss[] */
    private $spawned = [];

    /**
     * CombatManager constructor.
     * @param Cryptic $core
     * @throws BossException
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->listener = new CombatListener($core);
        $core->getServer()->getPluginManager()->registerEvents($this->listener, $core);
        $this->init();
    }

    /**
     * @throws BossException
     */
    public function init(): void {
        $this->addBoss(CorruptedKing::class);
        $this->addBoss(Alien::class);
        $this->addBoss(Witcher::class);
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return int
     */
    public function getGodAppleCooldown(CrypticPlayer $player): int {
        $cd = 0;
        if(isset($this->listener->godAppleCooldown[$player->getRawUniqueId()])) {
            if((40 - (time() - $this->listener->godAppleCooldown[$player->getRawUniqueId()])) > 0) {
                $cd = 40 - (time() - $this->listener->godAppleCooldown[$player->getRawUniqueId()]);
            }
        }
        return $cd;
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return int
     */
    public function getGoldenAppleCooldown(CrypticPlayer $player): int {
        $cd = 0;
        if(isset($this->listener->goldenAppleCooldown[$player->getRawUniqueId()])) {
            if((2 - (time() - $this->listener->goldenAppleCooldown[$player->getRawUniqueId()])) > 0) {
                $cd = 2 - (time() - $this->listener->goldenAppleCooldown[$player->getRawUniqueId()]);
            }
        }
        return $cd;
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return int
     */
    public function getEnderPearlCooldown(CrypticPlayer $player): int {
        $cd = 0;
        if(isset($this->listener->enderPearlCooldown[$player->getRawUniqueId()])) {
            if((10 - (time() - $this->listener->enderPearlCooldown[$player->getRawUniqueId()])) > 0) {
                $cd = 10 - (time() - $this->listener->enderPearlCooldown[$player->getRawUniqueId()]);
            }
        }
        return $cd;
    }

    /**
     * @param string $bossClass
     *
     * @throws BossException
     */
    public function addBoss(string $bossClass) {
        Entity::registerEntity($bossClass);
        if(isset($this->bosses[constant("$bossClass::BOSS_ID")])) {
            throw new BossException("Unable to register boss due to duplicated boss identifier");
        }
        $this->bosses[constant("$bossClass::BOSS_ID")] = $bossClass;
    }

    /**
     * @param int $identifier
     *
     * @return null|string
     */
    public function getBossNameByIdentifier(int $identifier): ?string {
        return $this->bosses[$identifier] ?? null;
    }

    /**
     * @param string $name
     *
     * @return int|null
     */
    public function getIdentifierByName(string $name): ?int {
        return array_search($name, $this->bosses) ?? null;
    }

    /**
     * @param int $bossId
     * @param Level $level
     * @param CompoundTag $tag
     */
    public function createBoss(int $bossId, Level $level, CompoundTag $tag) {
        $class = $this->getBossNameByIdentifier($bossId);
        /** @var Boss $entity */
        $entity = new $class($level, $tag);
        $entity->spawnToAll();
        $this->spawned{$entity->getId()} = $entity;
    }
}
