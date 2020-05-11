<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class EnvoysCommand extends Command {

    /**
     * EnvoysCommand constructor.
     */
    public function __construct() {
        parent::__construct("envoys", "List current envoys.");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        $lines = [];
        foreach($this->getCore()->getEnvoyManager()->getEnvoys() as $envoy) {
            $position = $envoy->getPosition();
            $x = $position->getFloorX();
            $y = $position->getFloorY();
            $z = $position->getFloorZ();
            $time = $envoy->getTimeLeft();
            $lines[] = TextFormat::RED . "Envoy ($x, $y, $z): " . TextFormat::WHITE . gmdate("i:s", $time) .  " before despawn.";
        }
        $sender->sendForm(new class($lines) extends CustomForm {

            /**
             *  constructor.
             *
             * @param string[] $lines
             */
            public function __construct(array $lines) {
                $elements = [];
                $elements[] = new Label("Message", implode("\n", $lines));
                parent::__construct(TextFormat::DARK_RED. TextFormat::BOLD . "Envoys", $elements);
            }
        });
    }
}