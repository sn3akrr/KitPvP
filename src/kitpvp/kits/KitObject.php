<?php namespace kitpvp\kits;

use pocketmine\{
	Player,
	Server
};
use pocketmine\item\Item;
use pocketmine\entity\Effect;
use pocketmine\utils\TextFormat;
use pocketmine\level\sound\{
	AnvilFallSound,
	AnvilUseSound
};
use kitpvp\KitPvP;
use kitpvp\kits\event\{
	KitEquipEvent,
	KitReplenishEvent
};
use kitpvp\combat\special\items\types\SpecialWeapon;

use core\Core;
use core\stats\Structure;

class KitObject{

	public $name;
	public $required_rank;
	public $price;

	public $items;
	public $armor;
	public $effects;

	public $abilities;
	public $special;

	public $cooldown;
	public $cooldowns = [];

	public function __construct(string $name, string $required_rank = "default", int $price = 0, array $items = [], array $armor = [], array $effects = [], array $abilities = [], array $special = [], $cooldown = 0){
		$this->name = $name;
		$this->required_rank = $required_rank;
		$this->price = $price;

		$this->items = $items;
		$this->armor = $armor;
		$this->effects = $effects;

		$this->abilities = $abilities;
		$this->special = $special;

		$this->cooldown = $cooldown;
	}

	public function __clone(){
		foreach($this->getAbilities() as $key => $ability){
			$this->abilities[$key] = clone $this->abilities[$key];
		}
	}

	public function getName(){
		return $this->name;
	}

	public function getRequiredRank(){
		return $this->required_rank;
	}

	public function getPrice(){
		return $this->price;
	}

	public function getItems(){
		return $this->items;
	}

	public function getArmorContents(){
		return $this->armor;
	}

	public function getEffects(){
		return $this->effects;
	}

	public function getAbilities(){
		return $this->abilities;
	}

	public function getAbility($name){
		foreach($this->getAbilities() as $ability){
			if($ability->getName() == $name) return $ability;
		}
		return null;
	}

	public function getSpecial(){
		return $this->special;
	}

	public function getCooldown(){
		return $this->cooldown;
	}

	public function hasPlayerCooldown(Player $player){
		return isset($this->cooldowns[$player->getName()]) && $this->cooldowns[$player->getName()] > 0;
	}

	public function getPlayerCooldown(Player $player){
		return $this->cooldowns[$player->getName()];
	}

	public function subtractPlayerCooldown(Player $player){
		if(!$this->hasPlayerCooldown($player)) return;
		$this->cooldowns[$player->getName()]--;
		if($this->cooldowns[$player->getName()] == 0) unset($this->cooldowns[$player->getName()]);
	}

	public function hasRequiredRank(Player $player){
		$prh = Structure::RANK_HIERARCHY[$player->getRank()];
		$rh = Structure::RANK_HIERARCHY[$this->getRequiredRank()];

		if($rh > $prh) return false;
		return true;
	}

	public function hasEnoughCurrency(Player $player){
		return $player->getTechits() >= $this->getPrice();
	}

	public function refund(Player $player){
		$player->addTechits($this->getPrice());
	}

	public function equip(Player $player){
		foreach($this->getItems() as $item){
			$player->getInventory()->addItem($item);
		}
		$player->getInventory()->setArmorContents($this->getArmorContents());
		foreach($this->getEffects() as $effect){
			$effect->setDuration(20 * 999999);
			$player->addEffect($effect);
		}
		foreach($this->getSpecial() as $special){
			$player->getInventory()->addItem($special);
		}
		foreach($this->getAbilities() as $ability){
			if($ability->activateOnEquip()) $ability->activate($player);
		}

		Server::getInstance()->getPluginManager()->callEvent(new KitEquipEvent($player, $this));
		$player->getLevel()->addSound(new AnvilFallSound($player), [$player]);

		$kits = KitPvP::getInstance()->getKits();
		$kits->getSession($player)->addKit($this);

		foreach($kits->kits as $kit){
			if($kit->getName() != $this->getName()){
				$kit->subtractPlayerCooldown($player);
			}
		}
		$kits->getBaseKit($this->getName())->cooldowns[$player->getName()] = $this->getCooldown();

		$num = $kits->getKitNum($this->getName());
		Core::getInstance()->getEntities()->getFloatingText()->getText("equipped-" . $num)->update($player, true);
	}

	public function purchase(Player $player){
		$player->takeTechits($this->getPrice());
		if($this->getCooldown() > 0) $this->cooldowns[$player->getName()] = $this->getCooldown();

		$this->equip($player);
	}

	public function replenish(Player $player){
		$player->getInventory()->clearAll();
		$player->removeAllEffects();

		$this->equip($player);
	}

	// TODO: Clean shit below //
	public function getItemList(){
		$list = [];
		$key = 0;
		foreach($this->getItems() as $item){
			$list[$key] = [
				"name" => $item->getName(),
				"count" => $item->getCount()
			];
			$key++;
		}
		return $list;
	}

	public function getSpecialList(){
		$list = [];
		$key = 0;
		foreach($this->getSpecial() as $special){
			$list[$key] = [
				"name" => $special->getName(),
				"count" => $special->getCount()
			];
			$key++;
		}
		return $list;
	}

	public function getVisualItemList(){
		$array = $this->getItemList();
		$string = "This kit comes with:\n\n  ".TextFormat::BOLD."Items:".TextFormat::RESET."\n    ";
		foreach($array as $key => $values){
			$string .= TextFormat::GREEN."x".$values["count"]." ".TextFormat::AQUA.$values["name"].TextFormat::GRAY.", ";
		}
		if(count($this->getEffects()) !== 0){
			$string .= "\n\n";
			$string .= "\n  ".TextFormat::RESET.TextFormat::BOLD."Effects:".TextFormat::RESET."\n    ";
			foreach($this->getEffects() as $effect){
				$string .= TextFormat::AQUA.$effect->getName()." ".TextFormat::YELLOW.$this->toRN($effect->getEffectLevel()).TextFormat::GRAY.", ";
			}
			$string .= "\n\n";
		}
		if(count($this->getAbilities()) !== 0){
			$string .= "\n  ".TextFormat::RESET.TextFormat::BOLD."Abilities:".TextFormat::RESET."\n";
			foreach($this->getAbilities() as $ability){
				$string .= "    ".TextFormat::DARK_RED.$ability->getName().TextFormat::GRAY." - ".TextFormat::RED.$ability->getDescription()."\n";
			}
			$string .= "\n\n";
		}
		if(count($this->getSpecial()) !== 0){
			$string .= TextFormat::RESET."  ".TextFormat::BOLD."Special Weapons:".TextFormat::RESET."\n    ";
			foreach($this->getSpecialList() as $key => $values){
				$string .= TextFormat::GOLD."x".$values["count"]." ".TextFormat::YELLOW.$values["name"].TextFormat::GRAY.", ";
			}
		}
		return $string;
	}

	public function toRN($num){
		$n = intval($num);
		$res = '';

		$roman_numerals = array(
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
		);

		foreach($roman_numerals as $roman => $number){
			$matches = intval($n / $number);
			$res .= str_repeat($roman, $matches);
			$n = $n % $number;
		}
		return $res;
	}

	public function canReplenish(Item $item){
		$no = [322];
		return !isset($no[$item->getId()]);
	}

}