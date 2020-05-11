<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\forms\RulesForm;
use core\command\utils\Command;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class RulesCommand extends Command {

    /** @var RulesForm */
    private $form;

    /**
     * SKitCommand constructor.
     */
    public function __construct() {
        parent::__construct("rules", "Shows you all server rules");
        $this->form = new RulesForm();
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