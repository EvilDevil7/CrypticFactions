<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\forms\ShopForm;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class ShopCommand extends Command {

    /** @var ShopForm */
    private $form;

    /**
     * ShopCommand constructor.
     */
    public function __construct() {
        parent::__construct("shop", "Open shop menu");
        $this->form = new ShopForm();
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