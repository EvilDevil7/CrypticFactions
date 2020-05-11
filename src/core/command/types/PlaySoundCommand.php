<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use ReflectionClass;
use ReflectionException;

class PlaySoundCommand extends Command {

    /**
     * PlaySoundCommand constructor.
     */
    public function __construct() {
        parent::__construct("playsound", "Play a sound (For testing purposes)", "/playsound <name>");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     * @throws ReflectionException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if((!$sender instanceof CrypticPlayer) or (!$sender->isOp())) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[0])) {
            $sender->sendMessage(Translation::getMessage("usageMessage", [
                "usage" => $this->getUsage()
            ]));
            return;
        }
        $name = $args[0];
        $pk = new LevelSoundEventPacket();
        $reflection = new ReflectionClass($pk);
        $const = $reflection->getConstants();
        if(array_key_exists($name, $const)) {
            $value = $const[$name];
        }
        else {
            $value = null;
        }
        if($value === null) {
            $sender->sendMessage(Translation::getMessage("invalidSound"));
            return;
        }
        $pk->position = $sender;
        $pk->sound = $value;
        $sender->sendDataPacket($pk);
    }
}