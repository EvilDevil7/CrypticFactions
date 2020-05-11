<?php

declare(strict_types = 1);

namespace core\rank;

use core\Cryptic;
use pocketmine\utils\TextFormat;

class RankManager {

    /** @var Cryptic */
    private $core;

    /** @var Rank[] */
    private $ranks = [];

    /**
     * RankManager constructor.
     *
     * @param Cryptic $core
     *
     * @throws RankException
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getServer()->getPluginManager()->registerEvents(new RankListener($core), $core);
        $this->init();
    }

    /**
     * @throws RankException
     */
    public function init(): void {
                $this->addRank(new Rank("Player", TextFormat::RESET, TextFormat::RESET . "§l§6PLAYER§r", Rank::PLAYER,
            "§7⚔ §3{faction_rank}{faction}§r §4{kills} {tag} §f{player}§7: §7{message}",
            "§3 {faction_rank}{faction}§r §4{kills} {tag} §f{player}", 5, 1, [
                "permission.starter",
                "permission.once"
            ]));
        $this->addRank(new Rank("Knight", TextFormat::RESET, TextFormat::RESET . "§l§3KNIGHT§r", Rank::KNIGHT,
            "§3⚔ §3{faction_rank}{faction} §l§3KNIGHT§r §4{kills} {tag} §f{player}§7: §3{message}",
            "§3{faction_rank}{faction} §l§3Knight§r §4{kills} {tag} §f{player}", 7, 2,  [
                "permission.starter",
                "permission.knight",
                "permission.once"
            ]));
        $this->addRank(new Rank("Wizard", TextFormat::RESET, TextFormat::RESET . "§l§9WIZARD§r", Rank::WIZARD,
            "§9⚔ §3{faction_rank}{faction} §l§9WIZARD§r §4{kills} {tag} §f{player}§7: §9{message}",
            "§3{faction_rank}{faction} §l§9Wizard§r §4{kills} {tag} §f{player}", 9, 3, [
                "permission.starter",
                "permission.knight",
                "permission.wizard",
                "permission.once"
            ]));
        $this->addRank(new Rank("King", TextFormat::RESET, TextFormat::RESET . "§l§eKING§r", Rank::KING,
            "§e⚔ §3{faction_rank}{faction} §l§eKING§r §4{kills} {tag} §f{player}§7: §e{message}",
            "§3{faction_rank}{faction} §l§eKing§r §4{kills} {tag} §f{player}", 11, 4, [
                "permission.starter",
                "permission.king",
                "permission.wizard",
                "permission.king",
                "permission.tier1",
                "permission.once"
            ]));
        $this->addRank(new Rank("Mystic", TextFormat::RESET, TextFormat::RESET . "§l§bMYSTIC§r", Rank::MYSTIC,
            "§b⚔ §3{faction_rank}{faction} §l§bMYSTIC§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§bMystic§r §4{kills} {tag} §f{player}", 13, 5, [
                "permission.starter",
                "permission.knight",
                "permission.wizard",
                "permission.king",
                "permission.mystic",
                "permission.tier1",
                "permission.tier2",
                "permission.once"
            ]));
        $this->addRank(new Rank("Cryptic", TextFormat::RESET, TextFormat::RESET . "§l§dCRYPTIC§r", Rank::CRYPTIC,
            "§3{faction_rank}{faction} §l§dCRYPTIC§r §4{kills} {tag} §f{player}§7: §d{message}",
            "§3{faction_rank}{faction} §l§dCryptic§r §4{kills} {tag} §f{player}", 15, 6, [
                "permission.starter",
                "permission.knight",
                "permission.wizard",
                "permission.king",
                "permission.mystic",
                "permission.cryptic",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.once"
            ]));
        $this->addRank(new Rank("God", TextFormat::RESET, TextFormat::RESET . "§l§cG§3O§cD§r", Rank::GOD,
            "§c⚔ §3{faction_rank}{faction} §l§cG§3O§cD§r §4{kills} {tag} §f{player}§7: §c{message}",
            "§3{faction_rank}{faction} §l§cG§3o§cd§r §4{kills} {tag} §f{player}", 15, 8, [
                "permission.starter",
                "permission.spartan",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.once",
                "permission.god"
            ]));
        $this->addRank(new Rank("Warlord", TextFormat::RESET, TextFormat::RESET . "§l§bWAR§dLORD§r", Rank::WARLORD,
            "§b⚔ §3{faction_rank}{faction} §l§bWAR§dLORD§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§bWar§dlord§r §4{kills} {tag} §f{player}", 18, 10, [
                "permission.starter",
                "permission.god",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.once",
                "permission.warlord"
            ]));
        $this->addRank(new Rank("Overlord", TextFormat::RESET, TextFormat::RESET . "§l§cOVER§6LORD§r", Rank::OVERLORD,
            "§3{faction_rank}{faction} §l§cOVER§6LORD§r §4{kills} {tag} §f{player}§7: §c{message}",
            "§3{faction_rank}{faction} §l§cOver§6lord§r §4{kills} {tag} §f{player}", 25, 15, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.once",
                "permission.join.full"
            ]));
        $this->addRank(new Rank("Trainee", TextFormat::RESET, TextFormat::RESET . "§l§bTRAINEE§r", Rank::TRAINEE,
            "§3{faction_rank}{faction} §l§bTRAINEE§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§bTrainee§r §4{kills} {tag} §f{player}", 20, 10, [
               "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.staff",
                "permission.join.full",
                "bansystem.command.kick",
                "bansystem.command.mutelist",
                "bansystem.command.tempmute",
                "permission.once"
            ]));
        $this->addRank(new Rank("Mod", TextFormat::RESET, TextFormat::RESET . "§l§cMOD§r", Rank::MODERATOR,
            "§3{faction_rank}{faction} §l§cMODERATOR§r §4{kills} {tag} §f{player}: §c{message}",
            "§3{faction_rank}{faction} §l§cModerator§r §4{kills} {tag} §f{player}", 25, 15, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.mod",
                "permission.join.full",
                "permission.staff",
                "bansystem.command.ban",
                "bansystem.command.banlist",
                "bansystem.command.kick",
                "bansystem.command.mute",
                "bansystem.command.mutelist",
                "bansystem.command.pardon",
                "bansystem.command.tempban",
                "bansystem.command.tempmute",
                "bansystem.command.unmute",
                "permission.once",
                "invsee"
            ]));
        $this->addRank(new Rank("Senior-Mod", TextFormat::RESET, TextFormat::RESET . "§l§cSENIOR MOD§r", Rank::SENIOR_MODERATOR,
            "§3{faction_rank}{faction} §l§cSENIOR MODERATOR§r §4{kills} {tag} §f{player}: §c{message}",
            "§3{faction_rank}{faction} §l§cSenior Moderator§r §4{kills} {tag} §f{player}", 30, 20, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.mod",
                "permission.join.full",
                "permission.staff",
                "bansystem.command.ban",
                "bansystem.command.banlist",
                "bansystem.command.kick",
                "bansystem.command.mute",
                "bansystem.command.mutelist",
                "bansystem.command.pardon",
                "bansystem.command.tempban",
                "bansystem.command.tempmute",
                "bansystem.command.unmute",
                "permission.once",
                "invsee"
            ]));
        $this->addRank(new Rank("Admin", TextFormat::RESET, TextFormat::RESET . "§l§4ADMIN§r", Rank::ADMIN,
            "§3{faction_rank}{faction} §l§4ADMIN§r §4{kills} {tag} §f{player}§7: §4{message}",
            "{tag} §3{faction_rank}{faction} §l§4Admin§r §4{kills} {tag} §f{player}", 35, 25, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.mod",
                "permission.join.full",
                "permission.staff",
                "pocketmine.command.teleport",
                "pocketmine.command.gamemode",
                "bansystem.command.ban",
                "bansystem.command.banlist",
                "bansystem.command.kick",
                "bansystem.command.mute",
                "bansystem.command.mutelist",
                "bansystem.command.pardon",
                "bansystem.command.tempban",
                "bansystem.command.tempmute",
                "bansystem.command.unmute",
                "permission.once",
                "invsee"
            ]));
        $this->addRank(new Rank("Senior-Admin", TextFormat::RESET, TextFormat::RESET . "§l§4SENIOR ADMIN§r", Rank::SENIOR_ADMIN,
            "§3{faction_rank}{faction} §l§4SENIOR ADMIN§r §4{kills} {tag} §f{player}§7: §4{message}",
            "§3{faction_rank}{faction} §l§4Senior Admin§r §4{kills} {tag} §f{player}", 40, 30, []));
        $this->addRank(new Rank("Manager", TextFormat::RESET, TextFormat::RESET . "§l§5MANAGER§r", Rank::MANAGER,
            "§3{faction_rank}{faction} §l§5MANAGER§r §4{kills} {tag} §f{player}§7: §d{message}",
            "§3{faction_rank}{faction} §l§5Manager§r §4{kills} {tag} §f{player}", 45, 35));
        $this->addRank(new Rank("Owner", TextFormat::RESET, TextFormat::RESET . "§l§dOWNER§r", Rank::OWNER,
            "§3{faction_rank}{faction} §l§dOWNER§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§dOwner§r §4{kills} {tag} §f{player}", 50, 40));
        $this->addRank(new Rank("YouTube", TextFormat::RESET, TextFormat::RESET . "§l§cY§fT§r", Rank::YOUTUBER,
            "§3{faction_rank}{faction} §l§cYOU§fTUBER§r §4{kills} {tag} §6{player}§7: §c{message}",
            "§3{faction_rank}{faction} §l§cYou§fTuber§r §4{kills} {tag} §6{player}", 20, 10, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.join.full",
                "permission.once"
            ]));
        $this->addRank(new Rank("Famous", TextFormat::RESET, TextFormat::RESET . "§l§dFAMOUS§r", Rank::FAMOUS,
            "§3{faction_rank}{faction} §l§dFAMOUS§r §4{kills} {tag} §f{player}§7: §d{message}",
            "§3{faction_rank}{faction} §l§dFamous§r §4{kills} {tag} §f{player}", 25, 15, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.join.full",
                "permission.once"
                ]));
       $this->addRank(new Rank("Developer", TextFormat::RESET, TextFormat::RESET . "§l§bDEVELOPER§r", Rank::DEVELOPER,
            "§3{faction_rank}{faction} §l§bDEVELOPER§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§bDeveloper§r §4{kills} {tag} §f{player}", 35, 30, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.mod",
                "permission.join.full",
                "permission.staff",
                "pocketmine.*",
                "pocketmine.command.teleport",
                "pocketmine.command.gamemode",
                "bansystem.command.ban",
                "bansystem.command.banlist",
                "bansystem.command.kick",
                "bansystem.command.mute",
                "bansystem.command.mutelist",
                "bansystem.command.pardon",
                "bansystem.command.tempban",
                "bansystem.command.tempmute",
                "bansystem.command.unmute",
                "permission.once",
                "invsee"
            ]));
        $this->addRank(new Rank("Builder", TextFormat::RESET, TextFormat::RESET . "§l§9BUILDER§r", Rank::BUILDER,
            "§3{faction_rank}{faction} §l§9BUILDER§r §4{kills} {tag} §f{player}§7: §b{message}",
            "§3{faction_rank}{faction} §l§9Builder§r §4{kills} {tag} §f{player}", 45, 35, [
                "permission.starter",
                "permission.god",
                "permission.warlord",
                "permission.overlord",
                "permission.tier1",
                "permission.tier2",
                "permission.tier3",
                "permission.mod",
                "permission.join.full",
                "permission.staff",
                "pocketmine.*",
                "pocketmine.command.teleport",
                "pocketmine.command.gamemode",
                "bansystem.command.ban",
                "bansystem.command.banlist",
                "bansystem.command.kick",
                "bansystem.command.mute",
                "bansystem.command.mutelist",
                "bansystem.command.pardon",
                "bansystem.command.tempban",
                "bansystem.command.tempmute",
                "bansystem.command.unmute",
                "permission.once",
                "invsee"
            ]));
    }

    /**
     * @param int $identifier
     *
     * @return Rank|null
     */
    public function getRankByIdentifier(int $identifier): ?Rank {
        return $this->ranks[$identifier] ?? null;
    }

    /**
     * @return Rank[]
     */
    public function getRanks(): array {
        return $this->ranks;
    }

    /**
     * @param string $name
     *
     * @return Rank
     */
    public function getRankByName(string $name): ?Rank {
        return $this->ranks[$name] ?? null;
    }

    /**
     * @param Rank $rank
     *
     * @throws RankException
     */
    public function addRank(Rank $rank): void {
        if(isset($this->ranks[$rank->getIdentifier()]) or isset($this->ranks[$rank->getName()])) {
            throw new RankException("Attempted to override a rank with the identifier of \"{$rank->getIdentifier()}\" and a name of \"{$rank->getName()}\".");
        }
        $this->ranks[$rank->getIdentifier()] = $rank;
        $this->ranks[$rank->getName()] = $rank;
    }
}
