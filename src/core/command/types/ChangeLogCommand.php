<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\forms\ChangeLogForm;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ChangeLogCommand extends Command {

    /** @var ChangeLogForm */
    private $form;

    /**
     * ChangeLogCommand constructor.
     */
    public function __construct() {
        parent::__construct("changelog", "Check the newest updates of this server.");
        $this->form = new ChangeLogForm();
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
        $sender->sendForm($this->form);
    }
}