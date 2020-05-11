<?php

declare(strict_types = 1);

namespace core\command\forms;

use libs\form\CustomForm;
use libs\form\element\Label;
use pocketmine\utils\TextFormat;

class ChangeLogForm extends CustomForm {

    /**
     * ChangeLogForm constructor.
     */
    public function __construct() {
        $title = TextFormat::BOLD . TextFormat::AQUA . "Change Log";
        $elements = [];
        $elements[] = new Label("Changes",  "This year is looking good so far! We've took your suggestions and added these following features!\n \n - New builds, expect to see a new PvP arena, a new boss arena, etc.\n- A new, epic mask system! This is more different from the original BeatsPE mask system and It's better.\n- Private vaults has been added! Ranks are now able to access more PV's.\n- Custom Potions have been added.\n- Custom Enchantments have been added.\n- Custom biomes have been added. Oooh, nice landscapes everywhere!\n- Shop has been updated. Our Shop feature in-game has been updated, and so much more has been added into it. Peek the new categories!\n- Envoys have been added. Go fetch yourself an envoy with some special goodies right now!\n- Head Hunting has been added.\n- Lucky blocks have been added. You already know what it is! Test your luck right now!\n- Ore Generators have been added. If you can see in the shop, we now have Ore Generators and Auto Ore Generators.\n- Quests have been added. Get quest points, then go to the quest shop and buy yourself the quest.\n- Bosses have been added. Get amazing rewards when killing a boss. Good luck!\n- Auction House has been added. Let other players buy your items through the Auction House system.\n- PVP HUD has been added. See your Armor durability, Golden Apple cooldown, Enchanted Golden Apple cooldown and your Ender Pearl cooldown.\n- Withdraw has been added. You can withdraw XP, money, or crate keys.\n- Report System has been added. See a hacker or someone being extremely toxic? Use the CrypticPE reporting system.\n- Coin Flip has been added. Test your luck with CoinFlip, It's like gambling!\n- Sacred Stones have been added. Check out our Sacred Stones.\n- Holy Boxes have been added. Holy Boxes give you a chance to win a G-Kits. On CrypticPE, we call them Sacred Kits.\n- Trading system has been added. Trade with players now with our trading system!\n- Trash system has been added. Dispose items in your inventory is not needed.\n- Tags have been added. Make yourself look cool with the tag on.\n- Faction Outpost has been added. It's like KOTH.. you'll get the idea, COMING SOON!");
        parent::__construct($title, $elements);
    }
}