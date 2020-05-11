<?php

declare(strict_types = 1);

namespace core\announcement;

use core\announcement\task\BroadcastMessagesTask;
use core\announcement\task\RestartTask;
use core\Cryptic;

class AnnouncementManager {

    /** @var Cryptic */
    private $core;

    /** @var RestartTask */
    private $restarter;

    /** @var string[] */
    private $messages;

    /** @var int */
    private $currentId = 0;

    /**
     * AnnouncementManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->restarter = new RestartTask($core);
        $this->init();
        $core->getScheduler()->scheduleRepeatingTask(new BroadcastMessagesTask($core), 4800);
        $core->getScheduler()->scheduleRepeatingTask($this->restarter, 20);
    }

    public function init(): void {
       $this->messages = [
            "\n§l§b » §r§7Follow our server Twitter to stay updated! §l§bTwitter: @TheCrypticPE§r§7.§r\n",
            "\n§l§b » §r§7Purchase ranks, perks and others at our store. Our store is located at §l§bstore.crypticpe.net§r§7.§r\n",
            "\n§l§b » §r§7Vote for our server, once you have voted, type §l§b/vote§r §7to receive rewards! §l§bVote at: vote.crypticpe.net§r§7.§r\n",
            "\n§l§b » §r§7To view all factions commands, type §l§b/f help§r§7.§r\n",
            "\n§l§b » §r§7Start a quest with the command, §l§b/quests§r§7.§r\n",
            "\n§l§b » §r§7Have a §l§bissue§r §7or a §l§bquestion§r§7? Ask our friendly staff members!§r\n",
            "\n§l§b » §r§7Purchase useful items with the command, §l§b/shop§r§7.§r\n",
            "\n§l§b » §r§7To check your coordinates, type the command §l§b/xyz on§r§7.§r\n",
            "\n§l§b » §r§7To list your items for other players to purchase, type the command, §l§b/ah§r§7.§r\n",
            "\n§l§b » §r§7Report hackers, bugs, and staff abuse with the command, §l§b/report§r§7.§r\n",
            "\n§l§b » §r§7Subscribe to §l§bJTJamez§r §7and §l§bJadynPlayzMC§r §7on §l§cYou§fTube§r§7!§r\n",
            "\n§l§b » §r§7View §l§b/rules§r §7to check on the server rules!§r§r\n",
            "\n§l§b » §r§7View §l§b/changelog§r §7to check on the latest updates!§r\n",
            "\n§l§b » §r§7Need to know the server IP and PORT? §l§bIP: CrypticPE.net§r §7| §l§bPORT: 19132§r§7.\n",
            "\n§l§b » §r§7Please invite your §l§bfriends§r §7to §l§bplay§r §7on our server!§r\n",
            "\n§l§b » §r§7Chat with our community, report issues and view updates on our Discord server. Join our Discord server now! §l§bDiscord: https://discord.gg/cXN8VTz§r§7.§r\n",
       ];
    }

    /**
     * @return string
     */
    public function getNextMessage(): string {
        if(isset($this->messages[$this->currentId])) {
            $message = $this->messages[$this->currentId];
            $this->currentId++;
            return $message;
        }
        $this->currentId = 0;
        return $this->messages[$this->currentId];
    }

    /**
     * @return RestartTask
     */
    public function getRestarter(): RestartTask {
        return $this->restarter;
    }
}
