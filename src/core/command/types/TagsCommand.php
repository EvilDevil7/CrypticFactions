<?php

declare(strict_types = 1);

namespace core\command\types;

use core\command\utils\Command;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TagsCommand extends Command {

    /**
     * TagsCommand constructor.
     */
    public function __construct() {
        parent::__construct("tags", "Tags command.");
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
        $manager = Cryptic::getInstance()->getTagManager();
        $tag = $manager->getTag($sender);
        $tags = $manager->getTagList($sender);

        if($tags == null){
            $tags = [];
        }
        if($tag == null){
            $tagformat = "DEFAULT.";
        }else{
            if(!isset($manager->tags[$tag])){
                $sender->sendMessage("§l§8(§c!§8)§r §7There was something, resetting you to default tag.§r");
                $sender->sendMessage("§l§8(§c!§8)§r §7Please retry the command.§r");
                $manager->setForceTag($sender, null);
                $tagformat = "DEFAULT.";
            }else{
                $tagformat = $manager->tags[$tag];
            }
        }
        $owned = [];
        $others = [];
        $form = new SimpleForm([$this, "onCheck"]);
        $form->setTitle("Tags");
        $form->setContent("§7Your current tag: " . $tagformat . "§r");
        foreach($manager->tags as $name => $format){
            if(in_array($name, $tags)){
                $owned[$name] = $format;
            }else{
                $others[$name] = $format;
            }
        }
        foreach($owned as $name => $format){
            $form->addButton(strval($format) . "\n§l§8[§6+§8]§r §7Owned§r §l§8[§6+§8]§r", -1, "", strval($name));
        }
        foreach($others as $name => $format){
            $form->addButton(strval($format), -1, "", strval($name));
        }
        $form->addButton("Reset", -1, "", "Reset");
        $sender->sendForm($form);
    }

    /**
     * @param Player $player
     * @param $data
     */
    public function onCheck(Player $player, $data): void{
        if($data !== null){
            $manager = Cryptic::getInstance()->getTagManager();
            $tags = $manager->getTagList($player);
            if($tags == null){
                $tags = [];
            }
            if($data == "Reset"){
                $data = null;
            }else{
                if(!in_array($data, $tags)){
                    $player->sendMessage("§l§8(§c!§8)§r §7You don't own the tag, §c" . $manager->tags[$data] . "§7.§r" );
                    return;
                }
            }
            $manager->setForceTag($player, $data);
            $player->sendMessage("§l§8(§a!§8)§r §7You've successfully updated your tag.§r");
        }
    }
}
