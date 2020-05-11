<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\Server;

class ClearlagCommand extends Command {

    /** @var array */
    protected $exemptedEntities = [];

    /**
     * ClearlagCommand constructor.
     */
    public function __construct() {
        parent::__construct("clearlag", "Clear lag command.");
    }

    public function getServer(): Server{
        return Server::getInstance();
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage("§l§8(§c!§8)§r §7Usage: /clearlag (check/clear/killmobs/clearall)");
            return;
        }
        switch($args[0]){
            case "clear":
                $sender->sendMessage("§l§8(§a!§8)§r §7Removed " . $this->removeEntities() . " entities.");
                break;
            case "check":
            case "count":
                $c = $this->getEntityCount();
                $sender->sendMessage("§l§8(§a!§8)§r §7There are " . $c[0] . " players, " . $c[1] . " mobs, and " . $c[2] . " entities.");
                break;
            case "killmobs":
                $sender->sendMessage("§l§8(§a!§8)§r §7Removed " . $this->removeMobs() . " mobs.");
                break;

            case "clearall":
            case "all":
                $sender->sendMessage("§l§8(§a!§8)§r §7Removed " . ($d = $this->removeMobs()) . " mob" . ($d == 1 ? "" : "s") . " and " . ($d = $this->removeEntities()) . " entit" . ($d == 1 ? "y" : "ies") . ".");
                break;
        }
    }

    /**
     * @return int
     */
    public function removeEntities(): int {
        $i = 0;
        foreach($this->getServer()->getLevels() as $level) {
            foreach($level->getEntities() as $entity) {
                if(!$this->isEntityExempted($entity) && !($entity instanceof Creature)) {
                    $entity->close();
                    $i++;
                }
            }
        }
        return $i;
    }

    /**
     * @return int
     */
    public function removeMobs(): int {
        $i = 0;
        foreach($this->getServer()->getLevels() as $level) {
            foreach($level->getEntities() as $entity) {
                if(!$this->isEntityExempted($entity) && $entity instanceof Creature && !($entity instanceof Human)) {
                    $entity->close();
                    $i++;
                }
            }
        }
        return $i;
    }

    /**
     * @return array
     */
    public function getEntityCount(): array {
        $ret = [0, 0, 0];
        foreach($this->getServer()->getLevels() as $level) {
            foreach($level->getEntities() as $entity) {
                if($entity instanceof Human) {
                    $ret[0]++;
                } else {
                    if($entity instanceof Creature) {
                        $ret[1]++;
                    } else {
                        $ret[2]++;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * @param Entity $entity
     */
    public function exemptEntity(Entity $entity): void {
        $this->exemptedEntities[$entity->getID()] = $entity;
    }

    /**
     * @param Entity $entity
     *
     * @return bool
     */
    public function isEntityExempted(Entity $entity): bool {
        return isset($this->exemptedEntities[$entity->getID()]);
    }
}
