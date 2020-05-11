<?php

declare(strict_types = 1);

namespace core\tag;


use core\Cryptic;
use core\CrypticPlayer;
use pocketmine\item\Item;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TagManager{

    /** @var array */
    public $tags = [];

    public function __construct(){
        Server::getInstance()->getPluginManager()->registerEvents(new TagListener($this), Cryptic::getInstance());
	    $this->register("Cryptic4Lyfe", "§8[§l§6CRYPTIC§e4§6LYFE§r§8]§r"); // This will be CRYPTIC4LYFE Tag.
	    $this->register("Godly", "§8[§l§bGOD§eLY§r§8]§r"); // This will be the Godly Tag.
	    $this->register("GOAT", "§8[§l§bGO§3AT§r§8]§r"); // This will be the GOAT Tag.
	    $this->register("OG", "§8[§6§ki§r§l§bO§eG§r§6§ki§r§8]§r"); // This will be the OG Tag.
	    $this->register("Sweat", "§8[§l§dSWEAT§r§8]§r"); // This will be the Sweat Tag.
	    $this->register("Tryhard", "§8[§l§4TRY§cHARD§r§8]§r"); // This will be Tryhard Tag.
	    $this->register("EGIRL", "§8[§l§5E§d-GIRL§r§8]§r"); // This will be the E-GIRL Tag.
	    $this->register("EBOY", "§8[§l§3E§b-BOY§r§8]§r"); // This will be the E-BOY Tag.
	    $this->register("BAE", "§8[§l§cB§eA§bE§r§8]§r"); // This will be the BAE Tag.
	    $this->register("BOT", "§9[§l§fBOT§r§9]§r"); // This will be the BOT Tag.
	    $this->register("OOF", "§8[§l§e§oOOF§r§8]§r"); // This will be the OOF Tag.
	    $this->register("MLG", "§8[§l§9M§fL§4G§8]§r"); // This will be the MLG Tag.
	    $this->register("420", "§8[§l§2420§r§8]§r"); // This will be the 420 Tag.
	    $this->register("Minemen", "§8[§l§3MINE§cMEN§r§8]§r"); // This will be the Minemen Tag.
	    $this->register("EZPZ", "§8[§l§6EZ§ePZ§r§8]§r"); // This will be the EZPZ Tag.
	    $this->register("BEERUS", "§8[§l§3BEE§bRUS§r§8]§r"); // This will be the BEERUS Tag.
	    $this->register("STOOPID", "§8[§l§9STOOPID§r§8]§r"); // This will be the STOOPID Tag.
	    $this->register("ESKETIT", "§8[§l§4ESKE§cTIT§r§8]§r"); // This will be the ESKETIT Tag.
	    $this->register("LITMAS", "§8[§l§bLIT§6MAS§r§8]§r"); // This will be the LITMAS Tag.
	    $this->register("VBUCKS", "§8[§l§fV§b-BUCKS§r§8]§r"); // This will be the VBUCKS Tag.
	    $this->register("IYKYK", "§8[§l§bI§eY§6K§eY§6K§r§8]§r"); // This will be the IYKYK Tag.
	    $this->register("FAME", "§8[§l§cF§7.§fA§7.§cM§7.§fE§r§8]§r"); // This will be the F.A.M.E Tag.
	    $this->register("Clickbait", "§8[§l§cCLICK§fBAIT§r§8]§r"); // This will be the Clickbait Tag.
	    $this->register("EpicGames", "§8[§l§cEPIC§6GAMES§8]§r"); // This will be the EpicGames Tag.
	    $this->register("DaddyJT", "§8[§l§dDaddy§5JT§r§8]§r"); // This will be the DaddyJT Tag.
	    $this->register("DaddyJadyn", "§8[§l§dDaddy§bJadyn§r§8]§r"); // This will be the DaddyJadyn Tag.
	    $this->register("SinksMinion", "§8[§l§bSinks§eMinion§r§8]§r"); // This will be the SinksMinion Tag.
	    $this->register("CubeIsThicc", "§8[§l§e§ki§r§bCube§3Is§l§bTHICC§r§e§ki§r§8]§r"); // This will be the CubeIsThicc Tag.
	    $this->register("ThiccBoi", "§8[§l§6THICC§eBoi§r§8]§r"); // This will be the ThiccBoi Tag.
	    $this->register("OkBoomer", "§8[§l§dOk§eBoomer§r§8]§r"); // This will be the OkBoomer Tag.
	    $this->register("9000IQ", "§8[§l§99000§1IQ§r§8]§r"); // This will be the 9000IQ Tag.
	    $this->register("SeasonOne", "§8[§l§eSeason§6One§r§8]§r"); // This will be the SeasonOne Tag.
	    $this->register("Lucky", "§8[§l§6LUC§eKY§r§8]§r"); // This will be the Lucky Tag.
	    $this->register("PvPGod", "§8[§l§4PvP§eGod§r§8]§r"); // This will be the PvPGod Tag.
	    $this->register("100CPS", "§8[§l§6100§6CPS§r§8]§r"); // This will be the 100CPS Tag.
	    $this->register("NoSkillz", "§8[§l§cNo§bSkillz§r§8]§r"); // This will be the NoSkillz Tag.
	    $this->register("SMOOVE", "§8[§l§aSMO§4OVE§r§8]§r"); // This will be the SMOOVE Tag.
	    $this->register("MakeCrypticGreatAgain", "§8[§f#§l§9Make§fCryptic§cGreat§fAgain§r§8]§r"); // This will be the MakeCrypticGreatAgain Tag.
	    $this->register("Supreme", "§c[§l§o§fSupreme§r§c]§r"); // This will be the Supreme Tag.
	    $this->register("OffWhite", "§8[§l§bOff§fWhite§r§8]§r"); // This will be the OffWhite Tag.
	    $this->register("Baller", "§8[§l§o§bBaller§r§8]§r"); // This will be the Baller Tag.
	    $this->register("OnJahNoCap", "§8[§l§eOn§bJah§cNo§eCap§r§8]§r"); // This will be the OnJahNoCap Tag.
	    $this->register("NotEvenCapping", "§8[§l§cNot§2Even§cCapping§r§8]§r"); // This will be the NotEvenCapping Tag.
	    $this->register("Gambler", "§8[§l§e§oGram§6bler§r§8]§r"); // This will be the Gambler Tag.
	    $this->register("Grinder", "§8[§l§4§oGRINDER§r§8]§r"); // This will be the Grinder Tag.
	    $this->register("COVID69", "§8[§l§aCOVID§2-69§r§8]§r"); // This will be the COVID-69 Tag.
	    $this->register("NoEffect", "§8[§l§cNo§4Effect§r§8]§r"); // This will be the NoEffect Tag.
	    $this->register("GangGang", "§8[§l§6Gang§eGang§r§8]§r"); // This will be the GangGang Tag.
	    $this->register("GangBang", "§8[§l§eGang§4Bang§r§8]§r"); // This will be the GangBang Tag.
	    $this->register("GucciGang", "§8[§l§2Guc§4ciG§2ang§r§8]§r"); // This will be GucciGang Tag.
	    $this->register("Mafia", "§8[§l§4Ma§cfia§r§8]§r"); // This will be the Mafia Tag.
	    $this->register("Clown", "§8[§l§fClo§4wn§r§8]§r"); // This will be the Clown Tag.
	    $this->register("WeDemBoyz", "§8[§l§o§eWe§6Dem§cBoyz§r§8]§r"); // This will be the WeDemBoyz Tag.
	    $this->register("TrashTalker", "§8[§l§2Trash§aTalker§r§8]§r"); // This will be the 2K20 Tag.
	    $this->register("2K20", "§8[§l§e2§6K§e20§r§8]§r"); // This will be the 2K20 Tag.
	    $this->register("Theory", "§8[§l§9Theory§r§8]§r"); // This will be the Theory Tag.
	    $this->register("AussiePing", "§8[§l§9Aussie§cPing§r§8]§r"); // This will be the AussiePing Tag.
	    $this->register("StreamSniping", "§8[§l§9Stream§dSniping§r§8]§r"); // This will be the StreamSniping Tag.
	    $this->register("YourInsaneJarvis", "§8[§l§o§9Your§dInsane§9Jarvis§r§8]§r"); // This will be the YourInsaneJarvis Tag.
	    $this->register("TakeTheL", "§8[§l§4Take§cThe§l§4L§r§8]§r"); // This will be the TakeTheL Tag.
	    $this->register("Toxic", "§8[§l§2TOXIC§r§8]§r"); // This will be the TOXIC Tag.
	    $this->register("Rusty", "§8[§l§6RUSTY§r§8]§r"); // This will be the RUSTY Tag.
	    $this->register("Bot", "§8[§l§9B§1O§9T§8]§r"); // This will be the BOT Tag.
	    $this->register("HypeBeast", "§8[§l§cHYPE§4BEAST§r§8]§r"); // This will be the HYPEBEAST Tag.
	    $this->register("Beats4Lyfe", "§8[§k§eii§r§l§bBeats4Life§r§k§eii§r§8]§r"); // This will be the BEATS4LYFE Tag.
    }

    /**
     * @param string $name
     * @param string $format
     */
    public function register(string $name, string $format): void{
        $this->tags[$name] = $format . TextFormat::RESET;
    }

    /**
     * @param CrypticPlayer $player
     * @param bool          $format
     * @return string|null
     */
    public function getTag(CrypticPlayer $player, bool $format = false): ?string{
        if(($tag = $player->getCurrentTag()) !== null){
            if($format){
                return $this->tags[$tag];
            }
            return $tag;
        }
        return null;
    }

    /**
     * @param CrypticPlayer $player
     * @return array|null
     */
    public function getTagList(CrypticPlayer $player): ?array{
        $tags = $player->getTags();
        if(count($tags) < 1) return null;
        return $tags;
    }

    /**
     * @param CrypticPlayer $player
     * @param string        $tag
     * @return bool
     */
    public function giveTag(CrypticPlayer $player, string $tag): bool{
        if(!isset($this->tags[$tag])){
            $player->sendMessage("§l§8(§c!§8)§r §7Tag doesn't exist, ($tag).");
            return false;
        }
        $player->addTag($tag);
        return true;
    }

    /**
     * @param CrypticPlayer $player
     * @param string        $tag
     */
    public function removeTag(CrypticPlayer $player, string $tag): void{
        if(!isset($this->tags[$tag])){
            $player->sendMessage("§l§8(§c!§8)§r §7Tag doesn't exist, ($tag).");
            return;
        }
        $player->removeTag($tag);
    }

    /**
     * @param CrypticPlayer $player
     * @param string        $tag
     * @return bool
     */
    public function setTag(CrypticPlayer $player, string $tag): bool{
        if(!isset($this->tags[$tag])){
            $player->sendMessage("§l§8(§c!§8)§r §7This Tag doesn't exist, ($tag).");
            return false;
        }
        $this->setForceTag($player, $tag);
        return true;
    }

    /**
     * @param CrypticPlayer $player
     * @param string|null   $tag
     */
    public function setForceTag(CrypticPlayer $player, ?string $tag): void{
        $player->setCurrentTag($tag);
    }

    /**
     * @param string $tag
     * @return Item
     */
    public function getTagNote(string $tag): Item{
        $item = Item::get(Item::PAPER);
        $item->setCustomName($this->tags[$tag] . " §7Tag§r");
        $item->setLore(["\n§l§8[§6+§8]§r §7Tap anywhere to claim this tag! §l§8[§6+§8]§r"]);
        $nbt = $item->getNamedTag();
        $nbt->setString("tag", $tag);
        $item->setNamedTag($nbt);
        return $item;
    }
}
