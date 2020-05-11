<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\forms\KitListForm;
use core\command\forms\KitsForm;
use core\command\utils\Command;
use core\kit\Kit;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;

class KitCommand extends Command {

    /** @var KitListForm */
    private $form;

    /**
     * KitCommand constructor.
     */
    public function __construct() {
        parent::__construct("kit", "Manage your kits.");
        $kits = $this->getCore()->getKitManager()->getKits();
        $list = [];
        foreach($kits as $kit) {
            if($kit->getRarity() <= Kit::UNCOMMON) {
                $list[] = $kit;
            }
        }
        $this->form = new KitListForm($list);
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
        if(isset($args[0]) and $args[0] == "all"){
            if($sender->isOp()){
                $kits = $this->getCore()->getKitManager()->getKits();
                $list = [];
                foreach($kits as $kit) {
                    $list[] = $kit;
                }
                $sender->sendForm(new KitsForm($list));
                return;
            }
            $sender->sendForm($this->form);
            return;
        }
        $sender->sendForm($this->form);
    }
}
