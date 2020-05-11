<?php

namespace core\clearlag\task;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Creature;
use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;

class ClearLagTask extends Task{

    protected $time = 600; // 10 mintues.

    public function onRun(int $currentTick){
        if(in_array($this->time, [30, 10, 5, 600])){
            $time = $this->time . " §7seconds§r";
            if($this->time >= 60){
                $time = floor(($this->time / 60) % 60) . " §7minutes§r";
            }
            Server::getInstance()->broadcastMessage("§l§b»§r §7Item drops will be removed in §b" . $time . "§r§7...§r");
        }
        if($this->time <= 0){
            $this->clearItems();
            $this->time = 600;
        }else{
            $this->time--;
        }
    }

    public function clearItems(): void{
        $count = 0;

        foreach(Server::getInstance()->getLevels() as $lvl){
            foreach($lvl->getEntities() as $en){
                if($en instanceof ItemEntity){
                    $count += 1;
                    $en->flagForDespawn();
                }
            }
        }
        Server::getInstance()->broadcastMessage("§l§b»§r §7A total of §8[§l§b" . $count . "§r§8]§r §7item drops have been removed.§r");
    }
}
