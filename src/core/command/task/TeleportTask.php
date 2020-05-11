<?php

declare(strict_types = 1);

namespace core\command\task;

use core\Cryptic;
use core\CrypticPlayer;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class TeleportTask extends Task {

    /** @var CrypticPlayer|null */
    private $player;

    /** @var Position */
    private $position;

    /** @var Position */
    private $originalLocation;

    /** @var int */
    private $time;

    /** @var int */
    private $maxTime;

    /**
     * TeleportTask constructor.
     *
     * @param CrypticPlayer $player
     * @param Position      $position
     * @param int           $time
     */
    public function __construct(CrypticPlayer $player, Position $position, int $time) {
        $this->player = $player;
        $areas = Cryptic::getInstance()->getAreaManager()->getAreasInPosition($player);
        if($areas !== null) {
            foreach($areas as $area) {
                if($area->getPvpFlag() === false) {
                    $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 200, 20));
                    $player->teleport($position);
                    $player->sendMessage(TextFormat::GREEN . "You have successfully teleport to your location.");
                    $player->getLevel()->addSound(new EndermanTeleportSound($player));
                    $this->player = null;
                    return;
                }
            }
        }
        $this->player->setTeleporting();
        $this->position = $position;
        $this->originalLocation = $player->asPosition();
        $this->time = $time;
        $this->maxTime = $time;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        if($this->player === null or $this->player->isClosed()) {
            Cryptic::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }
        if($this->player->distance($this->originalLocation) >= 3) {
            $this->player->setTeleporting(false);
            $this->player->addTitle(TextFormat::DARK_RED . "Failed to teleport", TextFormat::GRAY . "You must stand still!");
            Cryptic::getInstance()->getScheduler()->cancelTask($this->getTaskId());
            return;
        }
        if($this->time >= 0) {
            $this->player->addTitle(TextFormat::DARK_GREEN . "Teleporting in", TextFormat::GRAY . "$this->time seconds" . str_repeat(".", ($this->maxTime - $this->time) % 4));
            $this->time--;
            return;
        }
        if($this->player->isCreative() and !$this->player->getAllowFlight()){
            $this->player->setAllowFlight(true);
        }
        if($this->player->getAllowFlight() and (!$this->player->isCreative() or !$this->player->isSpectator())){
            $this->player->setAllowFlight(false);
            $this->player->setFlying(false);
        }
        $this->player->teleport($this->position);
        $this->player->sendMessage(TextFormat::GREEN . "You have successfully teleport to your location.");
        $this->player->getLevel()->addSound(new EndermanTeleportSound($this->player));
        $this->player->setTeleporting(false);
        $this->player->addEffect(new EffectInstance(Effect::getEffect(Effect::RESISTANCE), 200, 20));
        Cryptic::getInstance()->getScheduler()->cancelTask($this->getTaskId());
        return;
    }
}
