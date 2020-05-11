<?php

declare(strict_types = 1);

namespace core\command\forms;

use core\crate\Crate;
use core\item\enchantment\Enchantment;
use core\item\types\CrateKeyNote;
use core\item\types\MoneyNote;
use core\item\types\XPNote;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\form\CustomForm;
use libs\form\CustomFormResponse;
use libs\form\element\Dropdown;
use libs\form\element\Input;
use pocketmine\Player;

class WithdrawForm extends CustomForm {

    /**
     * WithdrawForm constructor.
     *
     * @param CrypticPlayer $player
     */
    public function __construct(CrypticPlayer $player) {
        $title = "§l§bWithdraw§r";
        $balance = $player->getBalance();
        $xp = $player->getCurrentTotalXp();
        $crateManager = $player->getCore()->getCrateManager();
        $vote = $player->getSession()->getKeys($crateManager->getCrate(Crate::VOTE));
        $common = $player->getSession()->getKeys($crateManager->getCrate(Crate::COMMON));
        $rare = $player->getSession()->getKeys($crateManager->getCrate(Crate::RARE));
        $epic = $player->getSession()->getKeys($crateManager->getCrate(Crate::EPIC));
        $legendary = $player->getSession()->getKeys($crateManager->getCrate(Crate::LEGENDARY));
        $tag = $player->getSession()->getKeys($crateManager->getCrate(Crate::TAG));
        $elements = [];
        $elements[] = new Dropdown("Options", "What would you like to withdraw?", [
            "Balance ($$balance)",
            "XP ($xp)",
            "Vote Crate Keys ($vote)",
            "Common Crate Keys ($common)",
            "Rare Crate Keys ($rare)",
            "Epic Crate Keys ($epic)",
            "Legendary Crate Keys ($legendary)",
            "Tags Crate Keys ($tag)",
        ]);
        $elements[] = new Input("Amount", "How many would you like to withdraw?");
        parent::__construct($title, $elements);
    }

    /**
     * @param Player $player
     * @param CustomFormResponse $data
     *
     * @throws TranslationException
     */
    public function onSubmit(Player $player, CustomFormResponse $data): void {
        if(!$player instanceof CrypticPlayer) {
            return;
        }
        if(count($player->getInventory()->getContents()) === $player->getInventory()->getSize()) {
            $player->sendMessage(Translation::getMessage("fullInventory"));
            return;
        }
        /** @var Dropdown $dropdown */
        $dropdown = $this->getElementByName("Options");
        $option = $dropdown->getOption($data->getInt("Options"));
        $amount = $data->getString("Amount");
        if(!is_numeric($amount)) {
            $player->sendMessage(Translation::getMessage("invalidAmount"));
            return;
        }
        $amount = (int)$amount;
        if($amount <= 0) {
            $player->sendMessage(Translation::getMessage("invalidAmount"));
            return;
        }
        $balance = $player->getBalance();
        $xp = $player->getCurrentTotalXp();
        $crateManager = $player->getCore()->getCrateManager();
        $vote = $player->getSession()->getKeys($crateManager->getCrate(Crate::VOTE));
        $common = $player->getSession()->getKeys($crateManager->getCrate(Crate::COMMON));
        $rare = $player->getSession()->getKeys($crateManager->getCrate(Crate::RARE));
        $epic = $player->getSession()->getKeys($crateManager->getCrate(Crate::EPIC));
        $legendary = $player->getSession()->getKeys($crateManager->getCrate(Crate::LEGENDARY));
        $tag = $player->getSession()->getKeys($crateManager->getCrate(Crate::TAG));
        switch($option) {
            case "Balance ($$balance)":
                if($amount > $balance) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $player->subtractFromBalance($amount);
                $player->getInventory()->addItem((new MoneyNote($amount))->getItemForm());
                break;
            case "XP ($xp)":
                if($amount > $xp) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                if($player->getInventory()->getItemInHand()->hasEnchantment(Enchantment::MENDING)) {
                    $player->sendMessage(Translation::getMessage("withdrawXpWhileMending"));
                    return;
                }
                $player->subtractXp($amount);
                $player->getInventory()->addItem((new XPNote($amount))->getItemForm());
                break;
            case "Vote Crate Keys ($vote)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::VOTE);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
            case "Common Crate Keys ($common)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::COMMON);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
            case "Rare Crate Keys ($rare)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::RARE);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
            case "Epic Crate Keys ($epic)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::EPIC);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
            case "Legendary Crate Keys ($legendary)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::LEGENDARY);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
            case "Tags Crate Keys ($tag)":
                if($amount > $rare) {
                    $player->sendMessage(Translation::getMessage("invalidAmount"));
                    return;
                }
                $crate = $crateManager->getCrate(Crate::TAG);
                $player->removeKeys($crate, $amount);
                $player->getInventory()->addItem((new CrateKeyNote($crate, $amount))->getItemForm());
                break;
        }
    }
}