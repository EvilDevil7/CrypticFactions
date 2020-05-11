<?php

declare(strict_types = 1);

namespace core\command;

use core\command\types\AddMoneyCommand;
use core\command\types\AddPermissionCommand;
use core\command\types\AddTagCommand;
use core\command\types\AddXPCommand;
use core\command\types\AliasCommand;
use core\command\types\BalanceCommand;
use core\command\types\BalanceTopCommand;
use core\command\types\BossCommand;
use core\command\types\BroadcastCommand;
use core\command\types\CEInfoCommand;
use core\command\types\ChangeLogCommand;
use core\command\types\ClearlagCommand;
use core\command\types\CustomPotionCommand;
use core\command\types\EChestCommand;
use core\command\types\EnchantCommand;
use core\command\types\EnvoysCommand;
use core\command\types\FeedCommand;
use core\command\types\FlyCommand;
use core\command\types\FreezeCommand;
use core\command\types\GayCommand;
use core\command\types\GiveItemCommand;
use core\command\types\GiveKeysCommand;
use core\command\types\GiveNoteCommand;
use core\command\types\HomeCommand;
use core\command\types\KeyAllCommand;
use core\command\types\KitCommand;
use core\command\types\ListCommand;
use core\command\types\MaskCommand;
use core\command\types\NickCommand;
use core\command\types\PayCommand;
use core\command\types\PingCommand;
use core\command\types\PlaySoundCommand;
use core\command\types\PVPCommand;
use core\command\types\PVPHUDCommand;
use core\command\types\QuestsCommand;
use core\command\types\RankUpCommand;
use core\command\types\RemoveHomeCommand;
use core\command\types\RemoveMoneyCommand;
use core\command\types\RenameCommand;
use core\command\types\RepairCommand;
use core\command\types\ReplyCommand;
use core\command\types\RestartCommand;
use core\command\types\RewardsCommand;
use core\command\types\RulesCommand;
use core\command\types\SacredAllCommand;
use core\command\types\SellCommand;
use core\command\types\SetHomeCommand;
use core\command\types\SetRankCommand;
use core\command\types\SetTagCommand;
use core\command\types\ShopCommand;
use core\command\types\SKitCommand;
use core\command\types\SpawnCommand;
use core\command\types\SpectateCommand;
use core\command\types\StaffChatCommand;
use core\command\types\StaffModeCommand;
use core\command\types\TagsCommand;
use core\command\types\TeleportAskCommand;
use core\command\types\TellCommand;
use core\command\types\TradeCommand;
use core\command\types\TrashCommand;
use core\command\types\VanishCommand;
use core\command\types\VoteCommand;
use core\command\types\WildCommand;
use core\command\types\WithdrawCommand;
use core\command\types\XYZCommand;
use core\faction\command\FactionCommand;
use core\gamble\command\CoinFlipCommand;
use core\Cryptic;
use pocketmine\command\Command;
use pocketmine\plugin\PluginException;

class CommandManager {

    /** @var Cryptic */
    private $core;

    /**
     * CommandManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $this->registerCommand(new AddMoneyCommand());
        $this->registerCommand(new RemoveMoneyCommand());
        $this->registerCommand(new AddPermissionCommand());
        $this->registerCommand(new AddXPCommand());
        $this->registerCommand(new AliasCommand());
        $this->registerCommand(new BalanceCommand());
        $this->registerCommand(new BalanceTopCommand());
        $this->registerCommand(new BroadcastCommand());
        $this->registerCommand(new CEInfoCommand());
        $this->registerCommand(new ChangeLogCommand());
        $this->registerCommand(new CoinFlipCommand());
        $this->registerCommand(new EnchantCommand());
        $this->registerCommand(new EnvoysCommand());
        $this->registerCommand(new FactionCommand());
        $this->registerCommand(new FeedCommand());
        $this->registerCommand(new FlyCommand());
        $this->registerCommand(new FreezeCommand());
        $this->registerCommand(new GiveItemCommand());
        $this->registerCommand(new GiveKeysCommand());
        $this->registerCommand(new HomeCommand());
        $this->registerCommand(new KeyAllCommand());
        $this->registerCommand(new KitCommand());
        $this->registerCommand(new PayCommand());
        $this->registerCommand(new PingCommand());
        $this->registerCommand(new PlaySoundCommand());
        $this->registerCommand(new PVPCommand());
        $this->registerCommand(new PVPHUDCommand());
        $this->registerCommand(new QuestsCommand());
        $this->registerCommand(new RankUpCommand());
        $this->registerCommand(new RemoveHomeCommand());
        $this->registerCommand(new RenameCommand());
        $this->registerCommand(new RepairCommand());
        $this->registerCommand(new ReplyCommand());
        $this->registerCommand(new RewardsCommand());
        $this->registerCommand(new SacredAllCommand());
        $this->registerCommand(new SellCommand());
        $this->registerCommand(new SetHomeCommand());
        $this->registerCommand(new SetRankCommand());
        $this->registerCommand(new ShopCommand());
        $this->registerCommand(new SKitCommand());
        $this->registerCommand(new SpawnCommand());
        $this->registerCommand(new SpectateCommand());
        $this->registerCommand(new StaffChatCommand());
        $this->registerCommand(new TeleportAskCommand());
        $this->registerCommand(new TellCommand());
        $this->registerCommand(new TrashCommand());
        $this->registerCommand(new VanishCommand());
        $this->registerCommand(new VoteCommand());
        $this->registerCommand(new WildCommand());
        $this->registerCommand(new WithdrawCommand());
        $this->registerCommand(new XYZCommand());
        $this->registerCommand(new MaskCommand());
        $this->registerCommand(new RulesCommand());
        $this->registerCommand(new ClearlagCommand());
        $this->registerCommand(new AddTagCommand());
        $this->registerCommand(new SetTagCommand());
        $this->registerCommand(new TagsCommand());
        $this->registerCommand(new BossCommand());
        $this->registerCommand(new GiveNoteCommand());
        $this->registerCommand(new CustomPotionCommand());
        $this->registerCommand(new ListCommand());
        $this->registerCommand(new RewardsCommand());
        $this->registerCommand(new RestartCommand());
        $this->registerCommand(new NickCommand());
        $this->registerCommand(new StaffModeCommand());
        $this->registerCommand(new RestartCommand());
        $this->registerCommand(new EChestCommand());
        $this->unregisterCommand("about");
        $this->unregisterCommand("me");
        $this->unregisterCommand("particle");
        $this->unregisterCommand("title");
        //$this->registerCommand(new TradeCommand());
        // $this->registerCommand(new InboxCommand());
    }

    /**
     * @param Command $command
     */
    public function registerCommand(Command $command): void {
        $commandMap = $this->core->getServer()->getCommandMap();
        $existingCommand = $commandMap->getCommand($command->getName());
        if($existingCommand !== null) {
            $commandMap->unregister($existingCommand);
        }
        $commandMap->register($command->getName(), $command);
    }

    /**
     * @param string $name
     */
    public function unregisterCommand(string $name): void {
        $commandMap = $this->core->getServer()->getCommandMap();
        $command = $commandMap->getCommand($name);
        if($command === null) {
            throw new PluginException("Invalid command: $name to un-register.");
        }
        $commandMap->unregister($commandMap->getCommand($name));
    }
}
