<?php

namespace core\data;

use core\crate\Crate;
use core\Cryptic;
use core\CrypticPlayer;
use core\faction\Faction;
use core\item\enchantment\Enchantment;
use core\libs\muqsit\invmenu\inventories\BaseFakeInventory;
use core\libs\muqsit\invmenu\InvMenu;
use core\price\event\ItemSellEvent;
use core\rank\Rank;
use core\translation\Translation;
use core\translation\TranslationException;
use libs\utils\BossBar;
use libs\utils\FloatingTextParticle;
use libs\utils\Scoreboard;
use libs\utils\UtilsException;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Durable;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Naddy {

    const PUBLIC = 0;

    const FACTION = 1;

    const ALLY = 2;

    const STAFF = 3;

    /** @var int */
    public $cps = 0;

    /** @var null|CommandSender */
    private $lastTalked = null;

    /** @var bool */
    private $vanish = false;

    /** @var Cryptic */
    private $core;

    /** @var bool */
    private $runningCrateAnimation = false;

    /** @var bool */
    private $autoSell = false;

    /** @var int */
    private $autoSellCooldown = 0;

    /** @var int[] */
    private $crates;

    /** @var bool */
    private $voteChecking = false;

    /** @var bool */
    private $voted = false;

    /** @var bool */
    private $teleporting = false;

    /** @var int */
    private $chatMode = self::PUBLIC;

    /** @var int */
    private $combatTag = 0;

    /** @var Scoreboard */
    private $scoreboard;

    /** @var BossBar */
    private $bossBar;

    /** @var FloatingTextParticle[] */
    private $floatingTexts = [];

    /** @var int */
    private $balance = 0;

    /** @var Rank */
    private $rank;

    /** @var string[] */
    private $permissions = [];

    /** @var string[] */
    private $permanentPermissions = [];

    /** @var string[] */
    private $tags = [];

    /** @var null|string */
    private $currentTag = null;

    /** @var null|Faction */
    private $faction = null;

    /** @var null|int */
    private $factionRole = null;

    /** @var int */
    private $kills = 0;

    /** @var int */
    private $luckyBlocks = 0;

    /** @var int */
    private $questPoints = 0;

    /** @var InvMenu */
    private $rewards;

    /** @var InvMenu */
    private $inbox;

    /** @var bool */
    private $breaking = false;

    /** @var Position[] */
    private $homes = [];

    /** @var int[] */
    private $teleportRequests = [];

    /** @var int[] */
    private $tradeRequests = [];

    /** @var array */
    private $activeArmorEnchantments = [];

    /** @var array */
    private $activeHeldItemEnchantments = [];

    /** @var int */
    private $rewardCooldown = 0;

    /** @var bool */
    private $pvpHud = false;

    /** @var bool $staffMode */
    protected $staffMode = false;
    /** @var Item[] $staffModeInventory */
    protected $staffModeInventory = [];

    public function __construct(Player $player, Naddy $data)
    {
    }

    /**
     * @param CommandSender $sender
     */
    public function setLastTalked(CommandSender $sender): void
    {
        $this->lastTalked = $sender;
    }

    /**
     * @return CommandSender|null
     */
    public function getLastTalked(): ?CommandSender
    {
        if ($this->lastTalked === null) {
            return null;
        }
        if (!$this->lastTalked instanceof CrypticPlayer) {
            return null;
        }
        return $this->lastTalked->isOnline() ? $this->lastTalked : null;
    }

    /**
     * @param Cryptic $core
     */
    public function load(Cryptic $core): void
    {
        $this->core = $core;
        $this->rewards = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->rewards->setName(TextFormat::YELLOW . "Rewards");
        $this->rewards->setListener(function (Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action): bool {
            if ($itemClickedWith->getId() !== Item::AIR) {
                return false;
            }
            if ($itemClicked->getId() === Item::AIR) {
                return false;
            }
            return true;
        });
        $this->rewards->setInventoryCloseListener(function (Player $player, BaseFakeInventory $inventory): void {
            $xuid = $player->getXuid();
            $items = Cryptic::encodeInventory($inventory);
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE rewards SET items = ? WHERE xuid = ?");
            $stmt->bind_param("ss", $items, $xuid);
            $stmt->execute();
            $stmt->close();
        });
        $this->inbox = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->inbox->setName(TextFormat::YELLOW . "Inbox");
        $this->inbox->setListener(function (Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $action): bool {
            if ($itemClickedWith->getId() !== Item::AIR) {
                return false;
            }
            if ($itemClicked->getId() === Item::AIR) {
                return false;
            }
            return true;
        });
        $this->inbox->setInventoryCloseListener(function (Player $player, BaseFakeInventory $inventory): void {
            $xuid = $player->getXuid();
            $items = Cryptic::encodeInventory($inventory);
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE inboxes SET items = ? WHERE xuid = ?");
            $stmt->bind_param("ss", $items, $xuid);
            $stmt->execute();
            $stmt->close();
        });
        $this->scoreboard = new Scoreboard($this);
        $this->bossBar = new BossBar($this);
        $this->register();
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT rare, legendary, mythic, ultra FROM crates WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($rare, $legendary, $mythic, $ultra);
        $stmt->fetch();
        $stmt->close();
        $this->crates[Crate::RARE] = $rare;
        $this->crates[Crate::LEGENDARY] = $legendary;
        $this->crates[Crate::MYTHIC] = $mythic;
        $this->crates[Crate::ULTRA] = $ultra;
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT faction, factionRole, balance, questPoints, rankId, permissions, tags, currentTag, kills, luckyBlocks FROM players WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($faction, $factionRole, $balance, $questPoints, $rankId, $permissions, $tags, $currentTag, $kills, $luckyBlocks);
        $stmt->fetch();
        $stmt->close();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT items FROM rewards WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($items);
        $stmt->fetch();
        $stmt->close();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT items FROM inboxes WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($inbox);
        $stmt->fetch();
        $stmt->close();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT name, x, y, z, level FROM homes WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($name, $x, $y, $z, $level);
        while ($stmt->fetch()) {
            $this->homes[$name] = new Position($x, $y, $z, $core->getServer()->getLevelByName($level));
        }
        $stmt->close();
        if ($items !== null) {
            $items = Cryptic::decodeInventory($items);
            foreach ($items as $item) {
                $this->rewards->getInventory()->addItem($item);
            }
        }
        if ($inbox !== null) {
            $items = Cryptic::decodeInventory($inbox);
            $i = 0;
            foreach ($items as $item) {
                $this->inbox->getInventory()->setItem($i, $item);
                ++$i;
            }
        }
        $this->rank = $core->getRankManager()->getRankByIdentifier($rankId);
        if ($faction !== null) {
            $faction = $core->getFactionManager()->getFaction($faction);
            if ($faction !== null) {
                if ($faction->isInFaction($this)) {
                    $this->faction = $faction;
                    $this->factionRole = $factionRole;
                }
            }
        }
        $this->balance = $balance;
        $this->permissions = explode(",", $permissions);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT permissions, rewardCooldown FROM extraData WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($permissions, $rewardCooldown);
        $stmt->fetch();
        $stmt->close();
        $this->rewardCooldown = $rewardCooldown;
        $this->permanentPermissions = explode(",", $permissions);
        $this->tags = explode(",", $tags);
        $this->currentTag = $currentTag;
        $this->setDisplayName($currentTag . TextFormat::RESET . TextFormat::WHITE . " " . $this->getName());
        $this->setNameTag($this->rank->getTagFormatFor($this, [
            "faction_rank" => $this->getFactionRoleToString(),
            "faction" => ($faction = $this->getFaction()) instanceof Faction ? $faction->getName() : "",
            "kills" => $this->getKills()
        ]));
        $this->setScoreTag(TextFormat::WHITE . floor($this->getHealth()) . TextFormat::RED . TextFormat::BOLD . " HP");
        $this->kills = $kills;
        $this->luckyBlocks = $luckyBlocks;
        $this->questPoints = $questPoints;
        foreach ($core->getKitManager()->getSacredKits() as $kit) {
            if ($this->hasPermission("permission.{$kit->getName()}")) {
                $this->addPermanentPermission("permission.{$kit->getName()}");
            }
        }
    }

    public function register(): void
    {
        $xuid = $this->getXuid();
        $username = $this->getName();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM players WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO players(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM rewards WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO rewards(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM inboxes WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO inboxes(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM crates WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO crates(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM kitCooldowns WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO kitCooldowns(uuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM kitCooldowns WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO kitCooldowns(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("SELECT username FROM extraData WHERE xuid = ?");
        $stmt->bind_param("s", $xuid);
        $stmt->execute();
        $stmt->bind_result($result);
        $stmt->fetch();
        $stmt->close();
        if ($result === null) {
            $username = $this->getName();
            $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO extraData(xuid, username) VALUES(?, ?)");
            $stmt->bind_param("ss", $xuid, $username);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * @param bool $value
     */
    public function vanish(bool $value = true): void
    {
        if ($value) {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                /** @var CrypticPlayer $player */
                if ($player->getRank()->getIdentifier() >= Rank::TRAINEE and $player->getRank()->getIdentifier() <= Rank::OWNER) {
                    continue;
                }
                $player->hidePlayer($this);
            }
        } else {
            foreach ($this->getServer()->getOnlinePlayers() as $player) {
                if (!$player->canSee($this)) {
                    $player->showPlayer($this);
                }
            }
        }
        $this->vanish = $value;
    }

    /**
     * @return bool
     */
    public function hasVanished(): bool
    {
        return $this->vanish;
    }

    /**
     * @return Cryptic
     */
    public function getCore(): Cryptic
    {
        return $this->core;
    }

    /**
     * @return bool
     */
    public function isRunningCrateAnimation(): bool
    {
        return $this->runningCrateAnimation;
    }

    /**
     * @param bool $value
     */
    public function setRunningCrateAnimation(bool $value = true): void
    {
        $this->runningCrateAnimation = $value;
    }

    /**
     * @return bool
     */
    public function isAutoSelling(): bool
    {
        return $this->autoSell;
    }

    /**
     * @param bool $value
     */
    public function setAutoSelling(bool $value = true): void
    {
        $this->autoSell = $value;
    }

    /**
     * @return bool
     */
    public function canAutoSell(): bool
    {
        return $this->getInventory()->firstEmpty() === -1 and (time() - $this->autoSellCooldown) > 10;
    }

    /**
     * @throws TranslationException
     */
    public function autoSell(): void
    {
        $this->autoSellCooldown = time();
        $sellables = $this->core->getPriceManager()->getSellables();
        $content = $this->getInventory()->getContents();
        /** @var Item[] $items */
        $items = [];
        $sellable = false;
        $entries = [];
        foreach ($content as $item) {
            if (!isset($sellables[$item->getId()])) {
                continue;
            }
            $entry = $sellables[$item->getId()];
            if (!$entry->equal($item)) {
                continue;
            }
            if ($sellable === false) {
                $sellable = true;
            }
            if (!isset($entries[$entry->getName()])) {
                $entries[$entry->getName()] = $entry;
                $items[$entry->getName()] = $item;
            } else {
                $items[$entry->getName()]->setCount($items[$entry->getName()]->getCount() + $item->getCount());
            }
        }
        if ($sellable === false) {
            return;
        }
        $price = 0;
        foreach ($entries as $entry) {
            $item = $items[$entry->getName()];
            $price += $item->getCount() * $entry->getSellPrice();
            $this->getInventory()->removeItem($item);
            $event = new ItemSellEvent($this, $item, $price);
            $event->call();
            $this->sendMessage(Translation::getMessage("sell", [
                "amount" => TextFormat::GREEN . $item->getCount(),
                "item" => TextFormat::DARK_GREEN . $entry->getName(),
                "price" => TextFormat::LIGHT_PURPLE . "$" . $price
            ]));
        }
        $this->addToBalance($price);
    }

    /**
     * @param Crate $crate
     *
     * @return int
     */
    public function getKeys(Crate $crate): int
    {
        return $this->crates[$crate->getName()];
    }

    /**
     * @param Crate $crate
     * @param int   $amount
     */
    public function addKeys(Crate $crate, int $amount): void
    {
        $identifier = $crate->getName();
        $this->crates[$identifier] += max(0, $amount);
        $identifier = strtolower($identifier);
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE crates SET $identifier = $identifier + ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
        $crate->updateTo($this);
    }

    /**
     * @param Crate $crate
     * @param int   $amount
     */
    public function removeKeys(Crate $crate, int $amount = 1): void
    {
        $identifier = $crate->getName();
        $this->crates[$identifier] -= max(0, $amount);
        $identifier = strtolower($identifier);
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE crates SET $identifier = $identifier - ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
        $crate->updateTo($this);
    }

    /**
     * @param bool $value
     */
    public function setCheckingForVote(bool $value = true): void
    {
        $this->voteChecking = $value;
    }

    /**
     * @return bool
     */
    public function isCheckingForVote(): bool
    {
        return $this->voteChecking;
    }

    /**
     * @return bool
     */
    public function hasVoted(): bool
    {
        return $this->voted;
    }

    /**
     * @param bool $value
     */
    public function setVoted(bool $value = true): void
    {
        $this->voted = $value;
    }

    /**
     * @return bool
     */
    public function isTeleporting(): bool
    {
        return $this->teleporting;
    }

    /**
     * @param bool $value
     */
    public function setTeleporting(bool $value = true): void
    {
        $this->teleporting = $value;
    }

    /**
     * @return int
     */
    public function getChatMode(): int
    {
        return $this->chatMode;
    }

    /**
     * @return string
     */
    public function getChatModeToString(): string
    {
        switch ($this->chatMode) {
            case self::PUBLIC:
                return "public";
            case self::FACTION:
                return "faction";
            case self::ALLY:
                return "ally";
            case self::STAFF:
                return "staff";
            default:
                return "unknown";
        }
    }

    /**
     * @param int $mode
     */
    public function setChatMode(int $mode): void
    {
        $this->chatMode = $mode;
    }

    /**
     * @param bool $value
     */
    public function combatTag(bool $value = true): void
    {
        if ($value) {
            $this->combatTag = time();
            return;
        }
        $this->combatTag = 0;
    }

    /**
     * @return bool
     */
    public function isTagged(): bool
    {
        return (time() - $this->combatTag) <= 15 ? true : false;
    }

    /**
     * @return int
     */
    public function getCombatTagTime(): int
    {
        return $this->combatTag;
    }

    /**
     * @return Scoreboard
     */
    public function getScoreboard(): Scoreboard
    {
        return $this->scoreboard;
    }

    /**
     * @return BossBar
     */
    public function getBossBar(): BossBar
    {
        return $this->bossBar;
    }

    /**
     * @return FloatingTextParticle[]
     */
    public function getFloatingTexts(): array
    {
        return $this->floatingTexts;
    }

    /**
     * @param string $identifier
     *
     * @return FloatingTextParticle|null
     */
    public function getFloatingText(string $identifier): ?FloatingTextParticle
    {
        return $this->floatingTexts[$identifier] ?? null;
    }

    /**
     * @param Position $position
     * @param string   $identifier
     * @param string   $message
     *
     * @throws UtilsException
     */
    public function addFloatingText(Position $position, string $identifier, string $message): void
    {
        if ($position->getLevel() === null) {
            throw new UtilsException("Attempt to add a floating text particle with an invalid level.");
        }
        $floatingText = new FloatingTextParticle($position, $identifier, $message);
        $this->floatingTexts[$identifier] = $floatingText;
        $floatingText->sendChangesTo($this);
    }

    /**
     * @param string $identifier
     *
     * @throws UtilsException
     */
    public function removeFloatingText(string $identifier): void
    {
        $floatingText = $this->getFloatingText($identifier);
        if ($floatingText === null) {
            throw new UtilsException("Failed to despawn floating text: $identifier");
        }
        $floatingText->despawn($this);
        unset($this->floatingTexts[$identifier]);
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @param int $amount
     */
    public function addToBalance(int $amount): void
    {
        $this->balance += $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET balance = balance + ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param int $amount
     */
    public function subtractFromBalance(int $amount): void
    {
        $this->balance -= $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET balance = balance - ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param int $amount
     */
    public function setBalance(int $amount): void
    {
        $this->balance = $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET balance = ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return Rank
     */
    public function getRank(): Rank
    {
        return $this->rank;
    }

    /**
     * @param Rank $rank
     */
    public function setRank(Rank $rank): void
    {
        $this->rank = $rank;
        $rankId = $rank->getIdentifier();
        foreach ($this->core->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if ($rankId >= Rank::TRAINEE and $rankId <= Rank::OWNER) {
                break;
            }
            /** @var CrypticPlayer $onlinePlayer */
            if ($onlinePlayer->hasVanished()) {
                $this->hidePlayer($onlinePlayer);
            }
        }
        $this->setNameTag($rank->getTagFormatFor($this, [
            "faction_rank" => $this->getFactionRoleToString(),
            "faction" => $this->faction instanceof Faction ? $this->faction->getName() : "",
            "kills" => $this->kills
        ]));
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET rankId = ? WHERE xuid = ?");
        $stmt->bind_param("is", $rankId, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param Permission|string $name
     *
     * @return bool
     */
    public function hasPermission($name): bool
    {
        if (in_array($name, $this->permissions)) {
            return true;
        }
        if (in_array($name, $this->rank->getPermissions())) {
            return true;
        }
        if (in_array($name, $this->permanentPermissions)) {
            return true;
        }
        return parent::hasPermission($name);
    }

    /**
     * @param string $permission
     */
    public function addPermission(string $permission): void
    {
        $this->permissions[] = $permission;
        $this->permissions = array_unique($this->permissions);
        $xuid = $this->getXuid();
        $permissions = implode(",", $this->permissions);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET permissions = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $permissions, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param string $permission
     */
    public function addPermanentPermission(string $permission): void
    {
        $this->permanentPermissions[] = $permission;
        $this->permanentPermissions = array_unique($this->permanentPermissions);
        $xuid = $this->getXuid();
        $permissions = implode(",", $this->permanentPermissions);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE extraData SET permissions = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $permissions, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return string
     */
    public function getCurrentTag(): string
    {
        return $this->currentTag;
    }

    /**
     * @param string $tag
     */
    public function setCurrentTag(string $tag): void
    {
        $this->currentTag = $tag;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET currentTag = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $tag, $xuid);
        $stmt->execute();
        $stmt->close();
        $this->setDisplayName($tag . TextFormat::RESET . " " . TextFormat::WHITE . $this->getName());
    }

    /**
     * @param string $tag
     */
    public function addTag(string $tag): void
    {
        $this->tags[] = $tag;
        $this->tags = array_unique($this->tags);
        $xuid = $this->getXuid();
        $tags = implode(",", $this->tags);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET tags = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $tags, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return Faction|null
     */
    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    /**
     * @param Faction|null $faction
     */
    public function setFaction(?Faction $faction): void
    {
        $this->faction = $faction;
        $this->setNameTag($this->rank->getTagFormatFor($this, [
            "faction_rank" => $this->getFactionRoleToString(),
            "faction" => ($faction = $this->getFaction()) instanceof Faction ? $faction->getName() : "",
            "kills" => $this->getKills()
        ]));
        $faction = $faction instanceof Faction ? $faction->getName() : null;
        $xuid = $this->getRawUniqueId();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET faction = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $faction, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int|null
     */
    public function getFactionRole(): ?int
    {
        return $this->factionRole;
    }

    /**
     * @return string
     */
    public function getFactionRoleToString(): string
    {
        switch ($this->factionRole) {
            case Faction::MEMBER:
                return "*";
            case Faction::OFFICER:
                return "**";
            case Faction::LEADER:
                return "***";
            case Faction::RECRUIT:
            default:
                return "";
        }
    }

    /**
     * @param int|null $role
     */
    public function setFactionRole(?int $role): void
    {
        $this->factionRole = $role;
        $this->setNameTag($this->rank->getTagFormatFor($this, [
            "faction_rank" => $this->getFactionRoleToString(),
            "faction" => ($faction = $this->getFaction()) instanceof Faction ? $faction->getName() : "",
            "kills" => $this->getKills()
        ]));
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET factionRole = ? WHERE xuid = ?");
        $stmt->bind_param("is", $role, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    public function playXpLevelUpSound(): void
    {
        $this->addXp(1000);
        $this->subtractXp(1000);
    }

    /**
     * @param int $amount
     */
    public function addKills(int $amount = 1): void
    {
        $this->kills += $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET kills = ? WHERE xuid = ?");
        $stmt->bind_param("is", $this->kills, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int
     */
    public function getKills(): int
    {
        return $this->kills;
    }

    /**
     * @param int $amount
     */
    public function addLuckyBlocksMined(int $amount = 1): void
    {
        $this->luckyBlocks += $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET luckyBlocks = ? WHERE xuid = ?");
        $stmt->bind_param("is", $this->luckyBlocks, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int
     */
    public function getLuckyBlocksMined(): int
    {
        return $this->luckyBlocks;
    }

    /**
     * @param Item $item
     */
    public function addReward(Item $item): void
    {
        if (!$this->rewards->getInventory()->canAddItem($item)) {
            $this->addTitle(TextFormat::DARK_RED . "Full Inventory", TextFormat::RED . "Clear out your rewards inventory to receive more!");
            return;
        }
        $this->rewards->getInventory()->addItem($item);
        $xuid = $this->getXuid();
        $items = Cryptic::encodeInventory($this->rewards->getInventory());
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE rewards SET items = ? WHERE xuid = ?");
        $stmt->bind_param("bs", $items, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    public function sendRewardsInventory(): void
    {
        $this->rewards->send($this);
    }

    /**
     * @param Item $item
     */
    public function addToInbox(Item $item): void
    {
        if ($this->inbox->getInventory()->firstEmpty() === -1) {
            $this->addTitle(TextFormat::DARK_RED . "Full Inventory", TextFormat::RED . "Clear out your inbox inventory to receive more!");
            return;
        }
        $this->inbox->getInventory()->setItem($this->inbox->getInventory()->firstEmpty(), $item);
        $xuid = $this->getXuid();
        $items = Cryptic::encodeInventory($this->inbox->getInventory());
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE inboxes SET items = ? WHERE xuid = ?");
        $stmt->bind_param("ss", $items, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    public function sendInboxInventory(): void
    {
        $this->inbox->send($this);
    }

    /**
     * @return InvMenu
     */
    public function getInbox(): InvMenu
    {
        return $this->inbox;
    }

    /**
     * @param int  $amount
     * @param bool $playSound
     *
     * @return bool
     */
    public function addXp(int $amount, bool $playSound = true): bool
    {
        if ($amount + $this->totalXp > 0x7fffffff) {
            return false;
        }
        static $mainHandIndex = -1;
        /** @var Durable[] $equipment */
        $equipment = [];
        if (($item = $this->inventory->getItemInHand()) instanceof Durable and $item->hasEnchantment(Enchantment::MENDING)) {
            $equipment[$mainHandIndex] = $item;
        }
        foreach ($this->armorInventory->getContents() as $k => $item) {
            if ($item instanceof Durable and $item->hasEnchantment(Enchantment::MENDING)) {
                $equipment[$k] = $item;
            }
        }
        if (!empty($equipment)) {
            /** @var int $k */
            $repairItem = $equipment[$k = array_rand($equipment)];
            if ($repairItem->getDamage() > 0) {
                $repairAmount = min($repairItem->getDamage(), $amount * 2);
                $repairItem->setDamage($repairItem->getDamage() - $repairAmount);
                $amount -= (int)ceil($repairAmount / 2);
                if ($k === $mainHandIndex) {
                    $this->inventory->setItemInHand($repairItem);
                } else {
                    $this->armorInventory->setItem($k, $repairItem);
                }
            }
        }
        return parent::addXp($amount, $playSound);
    }

    /**
     * @return bool
     */
    public function isBreaking(): bool
    {
        return $this->breaking;
    }

    /**
     * @param bool $value
     */
    public function setBreaking(bool $value = true): void
    {
        $this->breaking = $value;
    }

    /**
     * @return Position[]
     */
    public function getHomes(): array
    {
        return $this->homes;
    }

    /**
     * @param Position[] $homes
     */
    public function setHomes(array $homes): void
    {
        $this->homes = $homes;
    }

    /**
     * @param string $name
     *
     * @return null|Position
     */
    public function getHome(string $name): ?Position
    {
        return isset($this->homes[$name]) ? Position::fromObject($this->homes[$name]->add(0.5, 0, 0.5), $this->homes[$name]->getLevel()) : null;
    }

    /**
     * @param string   $name
     * @param Position $position
     */
    public function addHome(string $name, Position $position): void
    {
        $xuid = $this->getXuid();
        $username = $this->getName();
        $x = $position->getFloorX();
        $y = $position->getFloorY();
        $z = $position->getFloorZ();
        $level = $position->getLevel()->getName();
        $this->homes[$name] = $position;
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("INSERT INTO homes(xuid, username, name, x, y, z, level) VALUES(?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiiis", $xuid, $username, $name, $x, $y, $z, $level);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param string $name
     */
    public function deleteHome(string $name): void
    {
        $xuid = $this->getXuid();
        unset($this->homes[$name]);
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("DELETE FROM homes WHERE xuid = ? AND name = ?");
        $stmt->bind_param("ss", $xuid, $name);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return bool
     */
    public function isRequestingTeleport(CrypticPlayer $player): bool
    {
        return isset($this->teleportRequests[$player->getRawUniqueId()]) and (time() - $this->teleportRequests[$player->getRawUniqueId()]) < 30;
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addTeleportRequest(CrypticPlayer $player): void
    {
        $this->teleportRequests[$player->getRawUniqueId()] = time();
    }

    /**
     * @param CrypticPlayer $player
     */
    public function removeTeleportRequest(CrypticPlayer $player): void
    {
        if (isset($this->teleportRequests[$player->getRawUniqueId()])) {
            unset($this->teleportRequests[$player->getRawUniqueId()]);
        }
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return bool
     */
    public function isRequestingTrade(CrypticPlayer $player): bool
    {
        return isset($this->tradeRequests[$player->getRawUniqueId()]) and (time() - $this->tradeRequests[$player->getRawUniqueId()]) < 30;
    }

    /**
     * @param CrypticPlayer $player
     */
    public function addTradeRequest(CrypticPlayer $player): void
    {
        $this->tradeRequests[$player->getRawUniqueId()] = time();
    }

    /**
     * @param CrypticPlayer $player
     */
    public function removeTradeRequest(CrypticPlayer $player): void
    {
        if (isset($this->tradeRequests[$player->getRawUniqueId()])) {
            unset($this->tradeRequests[$player->getRawUniqueId()]);
        }
    }

    public function setActiveArmorEnchantments(): void
    {
        $this->activeArmorEnchantments = [];
        foreach ($this->getArmorInventory()->getContents() as $item) {
            if (!$item->hasEnchantments()) {
                continue;
            }
            foreach ($item->getEnchantments() as $enchantment) {
                $type = $enchantment->getType();
                if (!$type instanceof Enchantment) {
                    continue;
                }
                if (isset($this->activeArmorEnchantments[$type->getEventType()][$enchantment->getId()])) {
                    $this->activeArmorEnchantments[$type->getEventType()][$enchantment->getId()] = $enchantment->setLevel($this->activeArmorEnchantments[$type->getEventType()][$enchantment->getId()]->getLevel() + $enchantment->getLevel());
                }
                $this->activeArmorEnchantments[$type->getEventType()][$enchantment->getId()] = $enchantment;
            }
        }
        $this->removeEffect(Effect::NIGHT_VISION);
    }

    public function setActiveHeldItemEnchantments(): void
    {
        $this->activeHeldItemEnchantments = [];
        $item = $this->getInventory()->getItemInHand();
        if (!$item->hasEnchantments()) {
            return;
        }
        foreach ($item->getEnchantments() as $enchantment) {
            $type = $enchantment->getType();
            if (!$type instanceof Enchantment) {
                continue;
            }
            if (isset($this->activeHeldItemEnchantments[$type->getEventType()][$enchantment->getId()])) {
                $this->activeHeldItemEnchantments[$type->getEventType()][$enchantment->getId()] = $enchantment->setLevel($this->activeHeldItemEnchantments[$type->getEventType()][$enchantment->getId()]->getLevel() + $enchantment->getLevel());
            }
            $this->activeHeldItemEnchantments[$type->getEventType()][$enchantment->getId()] = $enchantment;
        }
    }

    /**
     * @return array
     */
    public function getActiveEnchantments(): array
    {
        $active = [];
        foreach ($this->activeArmorEnchantments as $eventType => $enchantments) {
            foreach ($enchantments as $id => $level) {
                $active[$eventType][$id] = $level;
            }
        }
        foreach ($this->activeHeldItemEnchantments as $eventType => $enchantments) {
            foreach ($enchantments as $id => $level) {
                $active[$eventType][$id] = $level;
            }
        }
        return $active;
    }

    /**
     * @param int $amount
     */
    public function addQuestPoints(int $amount = 1): void
    {
        $this->questPoints += $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET questPoints = ? WHERE xuid = ?");
        $stmt->bind_param("is", $this->questPoints, $xuid);
        $stmt->execute();
        $stmt->close();
    }


    /**
     * @return int
     */
    public function getQuestPoints(): int
    {
        return $this->questPoints;
    }

    /**
     * @param int $amount
     */
    public function subtractQuestPoints(int $amount): void
    {
        $this->questPoints -= $amount;
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE players SET questPoints = questPoints - ? WHERE xuid = ?");
        $stmt->bind_param("is", $amount, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return int
     */
    public function getRewardCooldown(): int
    {
        return $this->rewardCooldown;
    }

    public function setRewardCooldown(): void
    {
        $this->rewardCooldown = time();
        $xuid = $this->getXuid();
        $stmt = $this->core->getMySQLProvider()->getDatabase()->prepare("UPDATE extraData SET rewardCooldown = ? WHERE xuid = ?");
        $stmt->bind_param("is", $this->rewardCooldown, $xuid);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * @return bool
     */
    public function isUsingPVPHUD(): bool
    {
        return $this->pvpHud;
    }

    /**
     * @throws UtilsException
     */
    public function togglePVPHUD(): void
    {
        $this->pvpHud = !$this->pvpHud;
        if ($this->pvpHud === false) {
            $this->scoreboard->setScoreLine(7, "");
            $this->scoreboard->setScoreLine(8, TextFormat::RESET . TextFormat::AQUA . "store.crypticpe.net");
            $this->scoreboard->setScoreLine(9, TextFormat::RESET . TextFormat::AQUA . "vote.crypticpe.net");
            $this->scoreboard->removeLine(10);
            $this->scoreboard->removeLine(11);
        }
    }

    public function isInStaffMode(): bool
    {
        return $this->staffMode;
    }

    public function setStaffMode(bool $status = true): void
    {
        $this->staffMode = $status;
        if ($status) {
            $this->setStaffModeInventory($this->getInventory()->getContents());
            $this->getInventory()->clearAll();
            $this->setGamemode(self::CREATIVE);
            $this->vanish();
            $this->getInventory()->setItem(1, Item::get(Item::CONCRETE, $this->getChatMode() === self::STAFF ? 5 : 14, 1)->setCustomName($this->getChatMode() === self::STAFF ? TextFormat::ITALIC . TextFormat::GREEN . "Staff Chat" : TextFormat::ITALIC . TextFormat::RED . "Staff Chat"));
            $this->getInventory()->setItem(3, Item::get(Item::ICE, 0, 1)->setCustomName(TextFormat::ITALIC . TextFormat::AQUA . "Freeze/UnFreeze"));
            $this->getInventory()->setItem(5, Item::get(Item::MOB_HEAD, 3, 1)->setCustomName(TextFormat::ITALIC . TextFormat::LIGHT_PURPLE . "Teleport To Random Player"));
            $this->getInventory()->setItem(7, Item::get(Item::BOOK, 0, 1)->setCustomName(TextFormat::ITALIC . TextFormat::GOLD . "Inventory See"));
        } else {
            $this->getInventory()->clearAll();
            $this->getInventory()->setContents($this->getStaffModeInventory());
            $this->setGamemode(self::SURVIVAL);
            $this->vanish(false);
            $this->teleport($this->getCore()->getServer()->getDefaultLevel()->getSafeSpawn());
            $pk = new GameRulesChangedPacket();
            $pk->gameRules = [
                "showcoordinates" => [
                    1,
                    false
                ]
            ];
            $this->sendDataPacket($pk);
        }
    }

    public function getStaffModeInventory(): array
    {
        return $this->staffModeInventory;
    }

    public function setStaffModeInventory(array $inventory): void
    {
        $this->staffModeInventory = $inventory;
    }
}
