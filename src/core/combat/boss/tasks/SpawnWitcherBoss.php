<?php

namespace core\combat\boss\tasks;

use core\combat\boss\Boss;
use core\combat\boss\types\{Alien, Witcher, CorruptedKing};
use core\Cryptic;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class SpawnWitcherBoss extends Task{

    /** @var string */
    protected $prefix = "§l§8(§3!§8)§r §7";
    /** @var int */
    protected $time = 900; // 30 minutes.
    /** @var bool */
    protected $sentWarning = false;

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick){
        if(!$this->sentWarning and $this->check()){
            Server::getInstance()->broadcastMessage("§l§8(§3!§8)§r §7The §l§3Witcher§r §7boss spawning has been paused!§r");
            $this->sentWarning = true;
            return;
        }
        if($this->check()){
            return;
        }
        if($this->sentWarning){
            $this->sentWarning = false;
        }
        if(in_array($this->time, [1200, 600, 300, 30, 10, 5, 1800, 180, 60, 30, 20, 10, 5, 4, 3, 2, 1])){
            $time = $this->time . " §7seconds§r";
            if($this->time >= 60){
                $time = floor(($this->time / 60) % 60) . " §7minutes§r";
            }
            Server::getInstance()->broadcastMessage("§l§8(§3!§8)§r §7The §l§3Witcher§r §7boss is spawning in " . $time . "§7...§r");
        }
        if($this->time <= 0){
            if(!$this->check()) $this->summon();
            $this->time = 900;
        }else{
            $this->time--;
        }
    }

    /**
     * @return bool
     */
    public function check(): bool{
        $lvl = Server::getInstance()->getLevelByName("bossarena");
        foreach($lvl->getEntities() as $entity){
            if($entity instanceof Alien or $entity instanceof CorruptedKing or $entity instanceof Witcher){
                return true;
            }
        }
        return false;
    }

    public function summon(): void{
        $class = Cryptic::getInstance()->getCombatManager()->getBossNameByIdentifier(3);
        $lvl = Server::getInstance()->getLevelByName("bossarena");
        $pos = new Vector3(283, 75, 235);
        $lvl->loadChunk($pos->x, $pos->z);
        $nbt = Entity::createBaseNBT($pos);
        /** @var Boss $entity */
        $entity = new $class($lvl, $nbt);
        $entity->spawnToAll();
        Server::getInstance()->broadcastMessage("§l§8(§3!§8)§r §7The §l§3Witcher§r §7boss has been spawned in the boss arena. The player that deals the MOST damage gets 4 rewards! Everyone else gets 1 reward! To teleport to the boss arena, type /boss.§r");
    }
}
