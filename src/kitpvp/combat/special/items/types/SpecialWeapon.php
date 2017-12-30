<?php namespace kitpvp\combat\special\items\types;

use pocketmine\Player;
use pocketmine\event\entity\EntityDamageByEntityEvent;

use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\ListTag;

class SpecialWeapon extends Item{

	public function init(){
		$this->setCustomName(TextFormat::RESET . TextFormat::YELLOW . $this->getName());
		$lores = [];
		switch(true){
			case $this->isConsumable():
				switch(true){
					case $this->isMeleeWeapon():
						$lores[] = "Consumable Melee";
					break 2;
					default:
						$lores[] = "Consumable";
					break 2;
				}
			break;
			case $this->isShootable():
				$lores[] = "Shootable";
			break;
			case $this->isMeleeWeapon():
				$lores[] = "Melee";
			break;
		}
		$lores[] = " ";
		foreach(explode("\n", wordwrap($this->getDescription(), 30, "\n")) as $desc){
			$lores[] = TextFormat::GRAY . $desc;
		}
		foreach($lores as $key => $lore){
			$lores[$key] = TextFormat::RESET . TextFormat::GRAY . $lore;
		}
		$this->setLore($lores);

		$this->setNamedTagEntry(new ListTag("ench"));
	}

	public function isConsumable(){
		return false;
	}

	public function isThrowable(){
		return false;
	}

	public function isShootable(){
		return false;
	}

	public function isMeleeWeapon(){
		return false;
	}

	public function getDescription(){
		return "";
	}

	public function usePrimary(Player $player, Player $target, EntityDamageByEntityEvent $event){

	}

	public function useSecondary(Player $player){

	}

}