<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\Cryptic;
use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\utils\TextFormat;

class RulesForm extends CustomForm {

    /**
     * CEInfoForm constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::GREEN . "RULES";
        $elements[] = new Label("rules", file_get_contents(Cryptic::getInstance()->getDataFolder() . "rules.txt"));
        parent::__construct($title, $elements);
    }
}