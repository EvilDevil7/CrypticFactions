<?php

declare(strict_types = 1);

namespace core\announcement\task;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use pocketmine\command\ConsoleCommandSender;

class RestartTask extends Task {

    /** @var Cryptic */
    private $core;

    /** @var int */
    private $time = 5400;

    /**
     * RestartTask constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
    }

    /**
     * @param int $currentTick
     *
     * @throws TranslationException
     */
    public function onRun(int $currentTick) {
        $hours = floor($this->time / 3600);
        $minutes = floor(($this->time / 60) % 60);
        $seconds = $this->time % 60;
        if($minutes % 10 == 0 and $seconds == 0) {
            $this->core->getServer()->broadcastMessage(Translation::getMessage("restartMessage", [
                "hours" => $hours,
                "minutes" => $minutes,
                "seconds" => $seconds
            ]));
        }
        if($hours < 1) {
            if($minutes == 0 and $seconds == 5) {
                foreach($this->core->getServer()->getOnlinePlayers() as $player) {
                    if(!$player instanceof CrypticPlayer) {
                        continue;
                    }
                    $player->removeAllWindows();
                }
            }
            if($minutes == 0 and $seconds == 0) {
                $this->core->getServer()->dispatchCommand(new ConsoleCommandSender(), 'backup');
                foreach($this->core->getServer()->getOnlinePlayers() as $player) {
                    if(!$player instanceof CrypticPlayer) {
                        continue;
                    }
                    if($player->isTagged()) {
                        $player->combatTag(false);
                    }
                    $player->getSession()->save();
                    $player->close("", "§l§8(§c!§8)§r §7The server is restarting soon, therefore you cannot use commands until the server restarted.§r");
                    $player->teleport(Cryptic::getInstance()->getServer()->getDefaultLevel()->getSafeSpawn());
                }
                $this->core->getServer()->shutdown();
            }
        }
        $this->time--;
    }

    /**
     * @param int $time
     */
    public function setRestartProgress(int $time): void {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getRestartProgress(): int {
        return $this->time;
    }
}
