<?php

declare(strict_types = 1);

namespace core\kit;

use core\kit\types\StarterKit;
use core\kit\types\OnceKit;
use core\kit\types\KnightKit;
use core\kit\types\WizardKit;
use core\kit\types\KingKit;
use core\kit\types\MysticKit;
use core\kit\types\CrypticKit;
use core\kit\types\GodKit;
use core\kit\types\WarlordKit;
use core\kit\types\OverlordKit;
use core\kit\types\ArcherKit;
use core\kit\types\AssassinKit;
use core\kit\types\BanditKit;
use core\kit\types\EnchanterKit;
use core\kit\types\MinerKit;
use core\kit\types\ReaperKit;
use core\kit\types\SaintKit;
use core\Cryptic;

class KitManager {

    /** @var Cryptic */
	private $core;
	/** @var Kit[] */
	private $kits = [];
	/** @var Kit[] */
	private $sacredKits = [];
	/** @var array */
	private $data = [];

    /**
     * KitManager constructor.
     *
     * @param Cryptic $core
     *
     * @throws KitException
     */
	public function __construct(Cryptic $core) {
		$this->core = $core;
        if(file_exists($this->getCooldownPath() . DIRECTORY_SEPARATOR . "cooldown.json")){
            $this->data = json_decode(file_get_contents($this->getCooldownPath() . DIRECTORY_SEPARATOR . "cooldown.json"), true);
        }
		$this->init();
	}

    /**
     * @throws KitException
     */
	public function init(): void {
		$this->addKit(new StarterKit());
        $this->addKit(new OnceKit());
        $this->addKit(new KnightKit());
        $this->addKit(new WizardKit());
        $this->addKit(new KingKit());
        $this->addKit(new MysticKit());
        $this->addKit(new CrypticKit());
        $this->addKit(new GodKit());
        $this->addKit(new WarlordKit());
        $this->addKit(new OverlordKit());
        $this->addKit(new MinerKit());
        $this->addKit(new ArcherKit());
        $this->addKit(new ReaperKit());
        $this->addKit(new BanditKit());
        $this->addKit(new EnchanterKit());
        $this->addKit(new SaintKit($this));
        $this->addKit(new AssassinKit());
	}

    /**
     * @param string $kit
     *
     * @return Kit|null
     */
	public function getKitByName(string $kit) : ?Kit {
		return $this->kits[$kit] ?? null;
	}

    /**
     * @return Kit[]
     */
	public function getKits(): array {
	    return $this->kits;
    }

    /**
     * @return Kit[]
     */
    public function getSacredKits(): array {
        return $this->sacredKits;
    }

	/**
	 * @param Kit $kit
	 *
	 * @throws KitException
	 */
	public function addKit(Kit $kit) : void {
		if(isset($this->kits[$kit->getName()])) {
			throw new KitException("Attempted to override a kit with the name of \"{$kit->getName()}\" and a class of \"" . get_class($kit) . "\".");
		}
		$this->kits[$kit->getName()] = $kit;
		if($kit->getRarity() > Kit::UNCOMMON) {
		    $this->sacredKits[] = $kit;
        }
	}

    /**
     * @param string $kit
     * @param string $player
     * @return int
     */
	public function getCooldown(string $kit, string $player): int{
	    return isset($this->data[$kit][$player]) ? $this->data[$kit][$player] : 0;
    }

    /**
     * @param string $kit
     * @param string $player
     * @param int $time
     */
    public function addToCooldown(string $kit, string $player, int $time): void{
        $this->data[$kit][$player] = time();
        $this->save();
    }

    /**
     * @param string $kit
     * @param string $player
     */
    public function removeFromCooldown(string $kit, string $player): void{
	    if(isset($this->data[$kit][$player]))
        unset($this->data[$kit][$player]);
    }

    /**
     * @return string
     */
	public function getCooldownPath(): string{
	    return CrypticKit::getInstance()->getDataFolder() . "kit";
    }

    public function save(): void{
        file_put_contents($this->getCooldownPath() . DIRECTORY_SEPARATOR . "cooldown.json", json_encode($this->data, JSON_PRETTY_PRINT));
    }
}