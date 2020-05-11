<?php

declare(strict_types = 1);

namespace core\command\types;

use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MaskCommand extends Command {

    /**
     * InboxCommand constructor.
     */
    public function __construct() {
        parent::__construct("mask", "Mask command");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender->isOp()) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(!isset($args[0])){
            $sender->sendMessage("Usage: /mask <list|charm>");
            return;
        }
        if(!in_array($args[0], ["list", "charm"])){
            $amt = 1;
            if(isset($args[2])){
                $amt = intval($args[2]);
            }
            $target = $sender;
            if(isset($args[1])){
                $p = Server::getInstance()->getPlayer($args[1]);
                if($p instanceof CrypticPlayer){
                    $target = $p;
                }
            }
            foreach(Cryptic::getInstance()->getMaskManager()->getMasks() as $damage => $mask){
                $name = $mask->getName();
                $cmdName = strtolower(explode(" ", $name)[0]);

                /** @var Player $target */
                if(strtolower($args[0]) == $cmdName){
                    $item = Item::get(Item::SKULL, $damage, $amt);
                    $item->setCustomName("§l§d" . $name);
                    $item->setLore($mask->getLore());
                    $target->getInventory()->addItem($item);
                    $target->sendMessage("§l§8(§a!§8)§r §7You have gotten a §a" . $name . "§7.");
                    break;
                }
            }
            return;
        }

        switch($args[0]){
            case "list":
                $header = "§8--§l§dMasks List§r§8--" . TextFormat::RESET;
                foreach(Cryptic::getInstance()->getMaskManager()->getMasks() as $damage => $mask){
                    $header .= TextFormat::EOL . "- " . $mask->getName();
                }
                $sender->sendMessage($header);
                break;
            case "charm":
                $amt = 1;
                if(isset($args[2])){
                    $amt = intval($args[2]);
                }

                if(isset($args[1])){
                    if($args[1] == "all"){
                        foreach(Server::getInstance()->getOnlinePlayers() as $player) {
                            $name = $sender->getName();
                            $player->sendMessage("§l§8(§a!§8)§r §7$name have successfully given a Mask Charm to everyone that's online!");
                            $i = self::getMaskCharmItem($amt);
                            $player->getInventory()->addItem($i);
                        }
                    }else{
                        $player = Server::getInstance()->getPlayer($args[1]);
                        if($player instanceof CrypticPlayer){
                            $player->sendMessage("§l§8(§a!§8)§r §7You have successfully given a Mask Charm!");
                            $i = self::getMaskCharmItem($amt);
                            $player->getInventory()->addItem($i);
                        }else{
                            $sender->sendMessage(TextFormat::RED . "Player cannot be found.");
                        }
                    }
                }else{
                    if(!$sender instanceof CrypticPlayer){
                        $sender->sendMessage(TextFormat::RED . "Console not allow to do this");
                        return;
                    }
                    $sender->sendMessage("§l§8(§a!§8)§r §7You have successfully given a Mask Charm!");
                    $i = self::getMaskCharmItem($amt);
                    $sender->getInventory()->addItem($i);
                }
                break;
        }
    }

    /**
     * @param int $amt
     * @return Item
     */
    public static function getMaskCharmItem($amt = 1): Item{
        $i = Item::get(Item::ENCHANTED_BOOK, 101, $amt);
        $i->setCustomName("§l§aMask Charm§r");
        $i->setLore([
            "\n§fA mask charm that has the ability to form rare\nmasks that gives off special effects!\n\n§l§dRarities:\n§l§a*§r §fCommon\n§l§a*§r §fRare\n§l§a*§r §fEpic\n§l§a*§r §fLegendary\n\n§l§d(!) Tap anywhere to uncover this Mask Charm!"
        ]);
        return $i;
    }
}