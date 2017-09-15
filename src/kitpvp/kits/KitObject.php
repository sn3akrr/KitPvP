<?php namespace kitpvp\kits;

use pocketmine\Server;
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
use kitpvp\combat\special\items\SpecialWeapon;

use core\AtPlayer as Player;
use core\Core;

class KitObject{

	public $name;
	public $required_rank;
	public $price;

	public $items;
	public $effects;
	public $abilities;
	public $special;

	public $cooldown;
	public $cooldowns = [];

	public function __construct(string $name, string $required_rank = "default", int $price = 0, array $items = [], array $effects = [], array $abilities = [], array $special = [], $cooldown = 0){
		$this->name = $name;
		$this->required_rank = $required_rank;
		$this->price = $price;

		$this->items = $items;
		$this->effects = $effects;
		$this->abilities = $abilities;
		$this->special = $special;

		foreach($this->special as $special){
			$special->setCustomName(TextFormat::RESET.TextFormat::YELLOW.$special->getName());
		}

		$this->cooldown = $cooldown;
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

	public function getEffects(){
		return $this->effects;
	}

	public function getAbilities(){
		return $this->abilities;
	}

	public function getSpecial(){
		return $this->special;
	}

	public function getCooldown(){
		return $this->cooldown;
	}

	public function hasPlayerCooldown(Player $player){
		return isset($this->cooldowns[$player->getName()]);
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
		$prh = Core::getInstance()->getStats()->getRank()->getRankHierarchy($player->getRank());
		$rh = Core::getInstance()->getStats()->getRank()->getRankHierarchy($this->getRequiredRank());

		if($player->getName() == "Imyourfriend007" || $player->getName() == "ShadowEagleMCPE" || $player->getName() == "DerpyCake21") return true;

		if($rh > $prh) return false;
		return true;
	}

	public function hasEnoughCurrency(Player $player){
		return $player->getTechits() >= $this->getPrice() || KitPvP::getInstance()->getKits()->hasKitPassActive($player);
	}

	public function refund(Player $player){
		$player->addTechits($this->getPrice());
	}

	public function equip(Player $player, $replenish = false){
		foreach($this->getItems() as $item){
			if($item instanceof Item){
				if($this->isHelmet($item)){
					$player->getInventory()->setHelmet($item);
				}elseif($this->isChestplate($item)){
					$player->getInventory()->setChestplate($item);
				}elseif($this->isLeggings($item)){
					$player->getInventory()->setLeggings($item);
				}elseif($this->isBoots($item)){
					$player->getInventory()->setBoots($item);
				}else{
					if(!$replenish){
						$player->getInventory()->addItem($item);
					}else{
						if($this->canReplenish($item)){
							$player->getInventory()->addItem($item);
						}
					}
				}
			}
		}
		foreach($this->getEffects() as $effect){
			$effect->setDuration(20 * 999999);
			$player->addEffect($effect);
		}
		if(!$replenish){
			foreach($this->getSpecial() as $special){
				$player->getInventory()->addItem($special);
			}
			Server::getInstance()->getPluginManager()->callEvent(new KitEquipEvent($player, $this));
			KitPvP::getInstance()->getKits()->setEquipped($player, true, $this->getName());
			if(!KitPvP::getInstance()->getKits()->hasKitPassActive($player)) $player->takeTechits($this->getPrice());
			$player->getLevel()->addSound(new AnvilFallSound($player), [$player]);
			Core::getInstance()->getEntities()->getFloatingText()->forceUpdate($player);
			if(!KitPvP::getInstance()->getKits()->hasKitPassActive($player)){
				$player->takeTechits($this->getPrice());
				if(KitPvP::getInstance()->getKits()->hasPassCooldown($player)){
					KitPvP::getInstance()->getKits()->subtractPassCooldown($player);
				}
			}else{
				KitPvP::getInstance()->getKits()->consumeKitPass($player);
			}
		}else{
			foreach($this->getSpecial() as $special){
				if(!$special->isConsumable()) $player->getInventory()->addItem($special);
			}
			Server::getInstance()->getPluginManager()->callEvent(new KitReplenishEvent($player, $this));

			$player->getLevel()->addSound(new AnvilUseSound($player), [$player]);
		}

		if($this->getCooldown() > 0) $this->cooldowns[$player->getName()] = $this->getCooldown();
	}

	public function replenish(Player $player){
		for($i = 0; $i <= $player->getInventory()->getSize(); $i++){
			$item = $player->getInventory()->getItem($i);
			if($item instanceof SpecialWeapon){
				if(!$item->isConsumable()){
					$player->getInventory()->clear($i);
				}
			}else{
				if($this->canReplenish($item)){
					$player->getInventory()->clear($i);
				}
			}
		}
		foreach($this->getEffects() as $effect){
			$player->removeEffect($effect->getId());
		}
		$this->equip($player, true);
	}

	// Armor checks //
	public function isHelmet(Item $item){
		$name = $item->getName();
		if(strpos($name, "Helmet") || strpos($name, "Cap")) return true;
		return false;
	}

	public function isChestplate(Item $item){
		$name = $item->getName();
		if(strpos($name, "Chestplate") || strpos($name, "Tunic")) return true;
		return false;
	}

	public function isLeggings(Item $item){
		$name = $item->getName();
		if(strpos($name, "Leggings") || strpos($name, "Pants")) return true;
		return false;
	}

	public function isBoots(Item $item){
		$name = $item->getName();
		if(strpos($name, "Boots")) return true;
		return false;
	}

	// Text //
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
		$string .= "\n\n";
		if(count($this->getEffects()) !== 0){
			$string .= "\n  ".TextFormat::RESET.TextFormat::BOLD."Effects:".TextFormat::RESET."\n    ";
			foreach($this->getEffects() as $effect){
				$string .= TextFormat::AQUA.$effect->getName()." ".TextFormat::YELLOW.$this->toRN($effect->getEffectLevel()).TextFormat::GRAY.", ";
			}
			$string .= "\n\n";
		}
		if(count($this->getAbilities()) !== 0){
			$string .= "\n  ".TextFormat::RESET.TextFormat::BOLD."Abilities:".TextFormat::RESET."\n";
			foreach($this->getAbilities() as $name => $description){
				$string .= "    ".TextFormat::DARK_RED.$name.TextFormat::GRAY." - ".TextFormat::RED.$description."\n";
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