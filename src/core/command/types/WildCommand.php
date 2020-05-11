<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\task\TeleportTask;
use core\command\utils\Command;
use core\faction\Faction;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class WildCommand extends Command {

    /**
     * WildCommand constructor.
     */
    public function __construct() {
        parent::__construct("wild", "Teleport into the wilderness.", "/wild");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if($sender instanceof CrypticPlayer) {
            $level = $sender->getServer()->getLevelByName(Faction::CLAIM_WORLD);
            $position = $this->findLocation($level);
            $this->getCore()->getScheduler()->scheduleRepeatingTask(new TeleportTask($sender, $position, 5), 20);
            return;
        }
        $sender->sendMessage(Translation::getMessage("noPermission"));
        return;
    }

    /**
     * @param Level $level
     * @param int $loops
     *
     * @return Position
     */
    protected function findLocation(Level $level, int $loops = 0): Position {
        if($loops > 10) {
            $x = $level->getSpawnLocation()->getFloorX() + mt_rand(100, 1500);
            $y = 100;
            $z = $level->getSpawnLocation()->getFloorZ() + mt_rand(100, 1500);
            return new Position($x, $y, $z, $level);
        }
        $x = mt_rand(100, 1500);
        $z = mt_rand(100, 1500);
        $level->loadChunk($x, $z, true);
        $y = $level->getHighestBlockAt($x, $z);
        if($y < 0) {
            return $this->findLocation($level, ++$loops);
        }
        return $level->getSafeSpawn(new Vector3($x, $y, $z));
    }
}