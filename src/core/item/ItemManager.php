<?php

declare(strict_types = 1);

namespace core\item;

use core\item\enchantment\Enchantment;
use core\item\enchantment\EnchantmentListener;
use core\item\enchantment\types\AmplifyEnchantment;
use core\item\enchantment\types\AnnihilationEnchantment;
use core\item\enchantment\types\BleedEnchantment;
use core\item\enchantment\types\BlessEnchantment;
use core\item\enchantment\types\CharmEnchantment;
use core\item\enchantment\types\DrainEnchantment;
use core\item\enchantment\types\EvadeEnchantment;
use core\item\enchantment\types\FlingEnchantment;
use core\item\enchantment\types\GuillotineEnchantment;
use core\item\enchantment\types\HasteEnchantment;
use core\item\enchantment\types\HopsEnchantment;
use core\item\enchantment\types\ImmunityEnchantment;
use core\item\enchantment\types\JackpotEnchantment;
use core\item\enchantment\types\LuckEnchantment;
use core\item\enchantment\types\MonopolizeEnchantment;
use core\item\enchantment\types\NourishEnchantment;
use core\item\enchantment\types\ParalyzeEnchantment;
use core\item\enchantment\types\PerceptionEnchantment;
use core\item\enchantment\types\PiercingEnchantment;
use core\item\enchantment\types\QuickeningEnchantment;
use core\item\enchantment\types\ResistEnchantment;
use core\item\enchantment\types\ShatterEnchantment;
use core\item\enchantment\types\SlaughterEnchantment;
use core\item\enchantment\types\SmeltingEnchantment;
use core\item\enchantment\types\StunEnchantment;
use core\item\enchantment\types\VelocityEnchantment;
use core\item\enchantment\types\WitherEnchantment;
use core\Cryptic;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\ChainBoots;
use pocketmine\item\ChainHelmet;
use pocketmine\item\DiamondBoots;
use pocketmine\item\DiamondHelmet;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\FireAspectEnchantment;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\enchantment\SharpnessEnchantment;
use pocketmine\item\GoldBoots;
use pocketmine\item\GoldHelmet;
use pocketmine\item\IronBoots;
use pocketmine\item\IronHelmet;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\LeatherBoots;
use pocketmine\item\LeatherCap;
use pocketmine\item\Minecart;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\utils\TextFormat;

class ItemManager {

    /** @var Cryptic */
    private $core;

    /** @var Enchantment[] */
    private static $enchantments = [];

    /** @var array */
    private static $classifiedEnchantments = [];

    /**
     * ItemManager constructor.
     *
     * @param Cryptic $core
     */
    public function __construct(Cryptic $core) {
        $this->core = $core;
        $core->getServer()->getPluginManager()->registerEvents(new ItemListener($core), $core);
        $core->getServer()->getPluginManager()->registerEvents(new EnchantmentListener($core), $core);
        $this->init();
    }

    public function init() {
        self::registerEnchantment(new AmplifyEnchantment());
        self::registerEnchantment(new AnnihilationEnchantment());
        self::registerEnchantment(new BleedEnchantment());
        self::registerEnchantment(new BlessEnchantment());
        self::registerEnchantment(new CharmEnchantment());
        self::registerEnchantment(new DrainEnchantment());
        self::registerEnchantment(new EvadeEnchantment());
        self::registerEnchantment(new FlingEnchantment());
        self::registerEnchantment(new GuillotineEnchantment());
        self::registerEnchantment(new HasteEnchantment());
        self::registerEnchantment(new HopsEnchantment());
        self::registerEnchantment(new ImmunityEnchantment());
        self::registerEnchantment(new JackpotEnchantment());
        self::registerEnchantment(new LuckEnchantment());
        self::registerEnchantment(new MonopolizeEnchantment());
        self::registerEnchantment(new NourishEnchantment());
        self::registerEnchantment(new ParalyzeEnchantment());
        self::registerEnchantment(new PerceptionEnchantment());
        self::registerEnchantment(new PiercingEnchantment());
        self::registerEnchantment(new ResistEnchantment());
        self::registerEnchantment(new QuickeningEnchantment());
        self::registerEnchantment(new ShatterEnchantment());
        self::registerEnchantment(new SlaughterEnchantment());
        self::registerEnchantment(new SmeltingEnchantment());
        self::registerEnchantment(new StunEnchantment());
        self::registerEnchantment(new VelocityEnchantment());
        self::registerEnchantment(new WitherEnchantment());
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::UNBREAKING, "Unbreaking", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 15));
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::MENDING, "Mending", Enchantment::RARITY_RARE, Enchantment::SLOT_ALL, Enchantment::SLOT_NONE, 1));
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::LOOTING, "Looting", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_NONE, 5));
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::FORTUNE, "Fortune", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_DIG, Enchantment::SLOT_NONE, 2));
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::EFFICIENCY, "Efficiency", Enchantment::RARITY_COMMON, Enchantment::SLOT_DIG, Enchantment::SLOT_SHEARS, 20));
        self::registerEnchantment(new \pocketmine\item\enchantment\Enchantment(Enchantment::POWER, "Power", Enchantment::RARITY_COMMON, Enchantment::SLOT_BOW, Enchantment::SLOT_NONE, 7));
        self::registerEnchantment(new ProtectionEnchantment(Enchantment::PROTECTION, "Protection", Enchantment::RARITY_COMMON, Enchantment::SLOT_ARMOR, Enchantment::SLOT_NONE, 15, 0.75, null));
        self::registerEnchantment(new SharpnessEnchantment(Enchantment::SHARPNESS, "Sharpness", Enchantment::RARITY_COMMON, Enchantment::SLOT_SWORD, Enchantment::SLOT_AXE, 15));
        self::registerEnchantment(new FireAspectEnchantment(Enchantment::FIRE_ASPECT, "Fire Aspect", Enchantment::RARITY_MYTHIC, Enchantment::SLOT_SWORD, Enchantment::SLOT_NONE, 2));
        self::registerEnchantment(new ProtectionEnchantment(Enchantment::FEATHER_FALLING, "Feather Falling", Enchantment::RARITY_UNCOMMON, Enchantment::SLOT_FEET, Enchantment::SLOT_NONE, 4, 2.5, [
            EntityDamageEvent::CAUSE_FALL
        ]));
        ItemFactory::registerItem(new class() extends Minecart {

            /**
             * @return int
             */
            public function getMaxStackSize(): int {
                return 64;
            }
        }, true);
    }

    /**
     * @return Enchantment[]
     */
    public static function getEnchantments(): array {
        return self::$enchantments;
    }

    /**
     * @param $identifier
     *
     * @return \pocketmine\item\enchantment\Enchantment|null
     */
    public static function getEnchantment($identifier): ?\pocketmine\item\enchantment\Enchantment {
        return self::$enchantments[$identifier] ?? null;
    }


    /**
     * @param int|null $rarity
     *
     * @return \pocketmine\item\enchantment\Enchantment
     */
    public static function getRandomEnchantment(?int $rarity = null): \pocketmine\item\enchantment\Enchantment {
        if($rarity !== null) {
            /** @var \pocketmine\item\enchantment\Enchantment[] $enchantments */
            $enchantments = self::$classifiedEnchantments[$rarity];
            return $enchantments[array_rand($enchantments)];
        }
        return self::$enchantments[array_rand(self::$enchantments)];
    }

    /**
     * @param \pocketmine\item\enchantment\Enchantment $enchantment
     */
    public static function registerEnchantment(\pocketmine\item\enchantment\Enchantment $enchantment): void {
        Enchantment::registerEnchantment($enchantment);
        self::$enchantments[$enchantment->getId()] = $enchantment;
        self::$enchantments[$enchantment->getName()] = $enchantment;
        self::$classifiedEnchantments[$enchantment->getRarity()][] = $enchantment;
    }

    /**
     * @param int $integer
     *
     * @return string
     */
    public static function getRomanNumber(int $integer): string {
        $characters = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];
        $romanString = "";
        while($integer > 0) {
            foreach($characters as $rom => $arb) {
                if($integer >= $arb) {
                    $integer -= $arb;
                    $romanString .= $rom;
                    break;
                }
            }
        }
        return $romanString;
    }

    /**
     * @param Item $item
     * @param \pocketmine\item\enchantment\Enchantment $enchantment
     *
     * @return bool
     */
    public static function canEnchant(Item $item, \pocketmine\item\enchantment\Enchantment $enchantment): bool {
        if($item->hasEnchantment($enchantment->getId())) {
            if($item->getEnchantmentLevel($enchantment->getId()) < $enchantment->getMaxLevel()) {
                return true;
            }
            return false;
        }
        switch($enchantment->getPrimaryItemFlags()) {
            case Enchantment::SLOT_ALL:
                if($item instanceof Durable) {
                    return true;
                }
                break;
            case Enchantment::SLOT_FEET:
                if($item instanceof LeatherBoots or $item instanceof ChainBoots or $item instanceof GoldBoots or
                    $item instanceof IronBoots or $item instanceof DiamondBoots) {
                    return true;
                }
                break;
            case Enchantment::SLOT_HEAD:
                if($item instanceof LeatherCap or $item instanceof ChainHelmet or $item instanceof GoldHelmet or
                    $item instanceof IronHelmet or $item instanceof DiamondHelmet) {
                    return true;
                }
                break;
            case Enchantment::SLOT_ARMOR:
                if($item instanceof Armor) {
                    return true;
                }
                break;
            case Enchantment::SLOT_SWORD:
                if($item instanceof Sword) {
                    return true;
                }
                break;
            case Enchantment::SLOT_BOW:
                if($item instanceof Bow) {
                    return true;
                }
                break;
            case Enchantment::SLOT_DIG:
                if($item instanceof Tool) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * @param int $flag
     *
     * @return string
     */
    public static function flagToString(int $flag): string {
        switch($flag) {
            case Enchantment::SLOT_FEET:
                return "Boots";
                break;
            case Enchantment::SLOT_ARMOR:
                return "Armor";
                break;
            case Enchantment::SLOT_HEAD:
                return "Helmet";
                break;
            case Enchantment::SLOT_SWORD:
                return "Sword";
                break;
            case Enchantment::SLOT_BOW:
                return "Bow";
                break;
            case Enchantment::SLOT_DIG:
                return "Tools";
                break;
        }
        return "None";
    }


    /**
     * @param int $rarity
     *
     * @return string
     */
    public static function rarityToString(int $rarity): string {
        switch($rarity) {
            case Enchantment::RARITY_COMMON:
                return "Common";
                break;
            case Enchantment::RARITY_UNCOMMON:
                return "Uncommon";
                break;
            case Enchantment::RARITY_RARE:
                return "Rare";
                break;
            case Enchantment::RARITY_MYTHIC:
                return "Legendary";
                break;
            default:
                return "Unknown";
                break;
        }
    }

    /**
     * @param int $rarity
     *
     * @return string
     */
    public static function rarityToColor(int $rarity): string {
        switch($rarity) {
            case Enchantment::RARITY_COMMON:
                return TextFormat::BLUE;
                break;
            case Enchantment::RARITY_UNCOMMON:
                return TextFormat::DARK_BLUE;
                break;
            case Enchantment::RARITY_RARE:
                return TextFormat::LIGHT_PURPLE;
                break;
            case Enchantment::RARITY_MYTHIC:
                return TextFormat::AQUA;
                break;
            default:
                return "Unknown";
                break;
        }
    }

    /**
     * @param int $rarity
     *
     * @return float
     */
    public static function rarityToMultiplier(int $rarity): float {
        switch($rarity) {
            case Enchantment::RARITY_COMMON:
                return 1;
                break;
            case Enchantment::RARITY_UNCOMMON:
                return 1.25;
                break;
            case Enchantment::RARITY_RARE:
                return 1.5;
                break;
            case Enchantment::RARITY_MYTHIC:
                return 2;
                break;
            default:
                return 1;
                break;
        }
    }
}
