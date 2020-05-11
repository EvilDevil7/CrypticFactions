<?php

declare(strict_types = 1);

namespace core\envoy\task;

use core\envoy\EnvoyManager;
use core\faction\Faction;
use core\Cryptic;
use libs\utils\UtilsException;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class EnvoyHeartbeatTask extends Task {

    /** @var EnvoyManager */
    private $manager;

    /** @var Level */
    private $level;

    /**
     * EnvoyHeartbeatTask constructor.
     *
     * @param EnvoyManager $manager
     */
    public function __construct(EnvoyManager $manager) {
        $this->manager = $manager;
        $this->level = Server::getInstance()->getLevelByName(Faction::CLAIM_WORLD);
    }

    /**
     * @param int $currentTick
     *
     * @throws UtilsException
     */
    public function onRun(int $currentTick) {
        if(Cryptic::getInstance()->getAnnouncementManager()->getRestarter()->getRestartProgress() > 5) {
            if(count($this->manager->getEnvoys()) < 5) {
                $x = mt_rand(1, 2500);
                $z = mt_rand(1, 2500);
                $this->level->loadChunk($x, $z, true);
                $y = $this->level->getHighestBlockAt($x, $z);
                if($y < 0) {
                    return;
                }
                $position = $this->level->getSafeSpawn(new Vector3($x, $y, $z));
                $this->manager->spawnEnvoy($position);
                Server::getInstance()->broadcastMessage("\n\n§l§c** §r§cAN ENVOY HAS SPAWNED AT $x, $y, $z §l**\n§r§eUse /envoys to list active envoys.\n\n\n");
            }
            foreach($this->manager->getEnvoys() as $envoy) {
                $envoy->tick();
            }
            return;
        }
        if(count($this->manager->getEnvoys()) > 0) {
            foreach($this->manager->getEnvoys() as $envoy) {
                $envoy->despawn();
            }
        }
    }
}
