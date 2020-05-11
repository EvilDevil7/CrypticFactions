<?php

declare(strict_types=1);

namespace core\command\types;

use core\command\utils\Command;
use core\Cryptic;
use core\CrypticPlayer;
use core\translation\Translation;
use core\translation\TranslationException;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class StaffModeCommand extends Command{

	/**
	 * StaffChatCommand constructor.
	 */
	public function __construct(){
		parent::__construct("staffmode", "Toggle staff mode.", "/staffmode", ["sm"]);
	}

	/**
	 * @param CommandSender $sender
	 * @param string        $commandLabel
	 * @param array         $args
	 *
	 * @throws TranslationException
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if((!$sender instanceof CrypticPlayer) or (!$sender->hasPermission("permission.staff"))){
			$sender->sendMessage(Translation::getMessage("noPermission"));
			return;
		}
		$sender->setStaffMode(!$sender->isInStaffMode());
		$sender->sendMessage($sender->isInStaffMode() ? "§l§8(§a!§8)§r §7You have successfully §l§aENABLED§r §7staff mode!" : "§l§8(§c!§8)§r §7You have successfully §l§cDISABLED§r §7staff mode!");
	}
}