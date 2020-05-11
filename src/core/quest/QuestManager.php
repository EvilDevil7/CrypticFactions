<?php

declare(strict_types = 1);

namespace core\quest;

use core\Cryptic;
use core\CrypticPlayer;
use core\quest\types\BreakQuest;
use core\quest\types\BuyQuest;
use core\quest\types\KillQuest;
use core\quest\types\PlaceQuest;
use core\quest\types\SellQuest;
use pocketmine\block\Block;

class QuestManager {

    /** @var Cryptic */
    private $core;

    /** @var Session[] */
    private $sessions = [];

    /** @var Quest[] */
    private $quests = [];

    /** @var Quest[] */
    private $activeQuest = [];

    /**
     * QuestManager constructor.
     *
     * @param Cryptic $core
     *
     * @throws QuestException
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->init();
        while(count($this->activeQuest) < 3) {
            $this->activeQuest[] = $this->quests[array_rand($this->quests)];
        }
        $core->getServer()->getPluginManager()->registerEvents(new QuestListener($core), $core);
    }

    /**
     * @throws QuestException
     */
    public function init(): void {
        $this->addQuest(new BreakQuest("Noob Coal Miner", "Mine 50 coal ores.", 50, Quest::EASY, Block::COAL_ORE));
        $this->addQuest(new BreakQuest("Amateur Coal Miner", "Mine 100 coal ores.", 100, Quest::MEDIUM, Block::COAL_ORE));
        $this->addQuest(new BreakQuest("Pro Coal Miner", "Mine 250 coal ores.", 250, Quest::HARD, Block::COAL_ORE));
        $this->addQuest(new BreakQuest("Noob Redstone Miner", "Mine 50 redstone ores.", 50, Quest::EASY, Block::GLOWING_REDSTONE_ORE));
        $this->addQuest(new BreakQuest("Amateur Redstone Miner", "Mine 100 redstone ores.", 100, Quest::MEDIUM, Block::GLOWING_REDSTONE_ORE));
        $this->addQuest(new BreakQuest("Pro Redstone Miner", "Mine 250 redstone ores.", 250, Quest::HARD, Block::GLOWING_REDSTONE_ORE));
        $this->addQuest(new BreakQuest("Noob Iron Miner", "Mine 50 iron ores.", 50, Quest::EASY, Block::IRON_ORE));
        $this->addQuest(new BreakQuest("Amateur Iron Miner", "Mine 100 iron ores.", 100, Quest::MEDIUM, Block::IRON_ORE));
        $this->addQuest(new BreakQuest("Pro Iron Miner", "Mine 250 iron ores.", 250, Quest::HARD, Block::IRON_ORE));
        $this->addQuest(new BreakQuest("Noob Gold Miner", "Mine 50 gold ores.", 50, Quest::EASY, Block::GOLD_ORE));
        $this->addQuest(new BreakQuest("Amateur Gold Miner", "Mine 100 gold ores.", 100, Quest::MEDIUM, Block::GOLD_ORE));
        $this->addQuest(new BreakQuest("Pro Gold Miner", "Mine 250 gold ores.", 250, Quest::HARD, Block::GOLD_ORE));
        $this->addQuest(new BreakQuest("Noob Diamond Miner", "Mine 50 diamond ores.", 50, Quest::EASY, Block::DIAMOND_ORE));
        $this->addQuest(new BreakQuest("Amateur Diamond Miner", "Mine 100 diamond ores.", 100, Quest::MEDIUM, Block::DIAMOND_ORE));
        $this->addQuest(new BreakQuest("Pro Diamond Miner", "Mine 250 diamond ores.", 250, Quest::HARD, Block::DIAMOND_ORE));
        $this->addQuest(new BreakQuest("Noob Emerald Miner", "Mine 50 emerald ores.", 50, Quest::EASY, Block::EMERALD_ORE));
        $this->addQuest(new BreakQuest("Amateur Emerald Miner", "Mine 100 emerald ores.", 100, Quest::MEDIUM, Block::EMERALD_ORE));
        $this->addQuest(new BreakQuest("Pro Emerald Miner", "Mine 250 emerald ores.", 250, Quest::HARD, Block::EMERALD_ORE));
        $this->addQuest(new PlaceQuest("Noob Builder", "Place 50 cobblestone.", 50, Quest::EASY, Block::COBBLESTONE));
        $this->addQuest(new PlaceQuest("Amateur Builder", "Place 50 obsidian.", 50, Quest::MEDIUM, Block::OBSIDIAN));
        $this->addQuest(new PlaceQuest("Pro Builder", "Place 50 bedrock.", 50, Quest::HARD, Block::BEDROCK));
        $this->addQuest(new KillQuest("Murderer", "Kill 2 players.", 2, Quest::EASY));
        $this->addQuest(new KillQuest("Serial Killer", "Kill 5 players.", 5, Quest::MEDIUM));
        $this->addQuest(new KillQuest("Assassin", "Kill 10 players.", 10, Quest::HARD));
        $this->addQuest(new SellQuest("Noob Vendor", "Sell $1,000 worth of items.", 1000, Quest::EASY));
        $this->addQuest(new SellQuest("Amateur Vendor", "Sell $10,000 worth of items.", 10000, Quest::MEDIUM));
        $this->addQuest(new SellQuest("Pro Vendor", "Sell $100,000 worth of items.", 100000, Quest::HARD));
        $this->addQuest(new BuyQuest("Noob Spender", "Buy $10,000 worth of items.", 10000, Quest::EASY));
        $this->addQuest(new BuyQuest("Amateur Spender", "Sell $100,000 worth of items.", 100000, Quest::MEDIUM));
        $this->addQuest(new BuyQuest("Pro Spender", "Sell $1,000,000 worth of items.", 1000000, Quest::HARD));

    }

    /**
     * @param CrypticPlayer $player
     */
    public function addSession(CrypticPlayer $player): void {
        if(!isset($this->sessions[$player->getRawUniqueId()])) {
            $this->sessions[$player->getRawUniqueId()] = new Session($player);
        }
    }

    /**
     * @param CrypticPlayer $player
     *
     * @return Session
     */
    public function getSession(CrypticPlayer $player): Session {
        if(!isset($this->sessions[$player->getRawUniqueId()])) {
            $this->addSession($player);
        }
        return $this->sessions[$player->getRawUniqueId()];
    }

    /**
     * @return Quest[]
     */
    public function getQuests(): array {
        return $this->quests;
    }

    /**
     * @param string $name
     *
     * @return Quest|null
     */
    public function getQuest(string $name):?Quest {
        return $this->quests[$name] ?? null;
    }

    /**
     * @return Quest[]
     */
    public function getActiveQuests(): array {
        return $this->activeQuest;
    }

    /**
     * @param Quest $quest
     *
     * @throws QuestException
     */
    public function addQuest(Quest $quest): void {
        if(isset($this->quests[$quest->getName()])) {
            throw new QuestException("Attempt to override an existing quest named: " . $quest->getName());
        }
        $this->quests[$quest->getName()] = $quest;
    }
}