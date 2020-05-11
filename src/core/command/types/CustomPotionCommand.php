<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CustomPotionCommand extends Command {

    /**
     * TagsCommand constructor.
     */
    public function __construct() {
        parent::__construct("custompotion", "CustomPotion command.");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @throws TranslationException
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if(!$sender instanceof CrypticPlayer or !$sender->hasPermission("cryptic.custompotions")) {
            $sender->sendMessage(Translation::getMessage("noPermission"));
            return;
        }
        if(isset($args[0])){
            if(!$player = $sender->getServer()->getPlayer($args[0])){
                $sender->sendMessage("§l§8(§c!§8)§r §7That player does not exist!§r");
                return;
            }
            $name = $player->getName();
            if(isset($args[1])){
                switch($args[1]){
                    case "raiding":
                        $sender->sendMessage("§l§8(§a!§8)§r §7You have given " . $name . " a §l§cRaiding Elixir§r§7.§r");
                        $raiding = Item::get(Item::POTION, 100, 1);
                        $raiding->setCustomName("§l§cRaiding Elixir§r");
                        $raiding->setLore([
                            "\n§8* §aSpeed I §7(6:00)\n§8* §aHaste II §7(6:00)\n§8* §aNight Vision §7(3:00)§r"
                        ]);
                        $player->getInventory()->addItem($raiding);
                        break;
                        case "pvp":
                            $sender->sendMessage("§l§8(§a!§8)§r §7You have given " . $name . " a §l§bPvP Elixir§r§7.§r");
                            $pvp = Item::get(Item::POTION, 101, 1);
                            $pvp->setCustomName("§l§bPvP Elixir§r");
                            $pvp->setLore([
                                "\n§8* §aJump Boost I §7(3:00)\n§8* §aStrength I §7(0:30)\n§8* §aNight Vision §7(6:00)\n§8* §aFire Resistance §7(6:00)§r"
                            ]);
                            $player->getInventory()->addItem($pvp);
                            break;
                        case "healer":
                            $sender->sendMessage("§l§8(§a!§8)§r §7You have given " . $name . " a §l§aHealer Elixir§r§7.§r");

                            $healer = Item::get(Item::POTION, 102, 1);
                            $healer->setCustomName("§l§eHealer Elixir§r");
                            $healer->setLore([
                                "\n§8* §aRegeneration II §7(3:00)\n§8* §aAbsorption II §7(3:00)§r"
                            ]);
                            $player->getInventory()->addItem($healer);
                            break;
                        case "mining":
                            $sender->sendMessage("§l§8(§a!§8)§r §7You have given " . $name . " a §l§dMining Elixir§r§7.§r");

                            $miner = Item::get(Item::POTION, 103, 1);
                            $miner->setCustomName("§l§dMining Elixir§r");
                            $miner->setLore([
                                "\n§8* §aSpeed III §7(3:00)\n§8* §aHaste III §7(3:00)\n§8* §aFire Resistance II §7(3:00)\n§8* §aWater Breathing II §7(3:00)\n§8* §aNight Vision II §7(3:00)§r"
                            ]);
                            $player->getInventory()->addItem($miner);
                            break;
                    }
                }
            }
    }

    /**
     * @param Player $player
     * @param $data
     */
    public function onCheck(Player $player, $data): void{
        if($data !== null){
            $manager = Cryptic::getInstance()->getTagManager();
            /** @var CrypticPlayer $player */
            $tags = $manager->getTagList($player);
            if($data == "Reset"){
                $data = null;
            }else{
                if(!in_array($data, $tags)){
                    $player->sendMessage("§l§8(§c!§8)§r §7You dont have tag: " . $manager->tags[$data]);
                    return;
                }
            }
            $manager->setForceTag($player, $data);
            $player->sendMessage("§l§8(§a!§8)§r §7You've successfully set your tag.§r");
        }
    }
}
