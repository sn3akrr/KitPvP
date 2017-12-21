<?php namespace kitpvp\combat\special\items\types;

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
						$lores[] = TextFormat::GRAY . "Consumable Melee";
					break 2;
					default:
						$lores[] = TextFormat::GRAY . "Consumable";
					break 2;
				}
			break;
			case $this->isShootable():
				$lores[] = TextFormat::GRAY . "Shootable";
			break;
			case $this->isMeleeWeapon():
				$lores[] = TextFormat::GRAY . "Melee";
			break;
		}
		$lores[] = " ";
		foreach(explode("\n", wordwrap($this->getDescription(), 20, "\n")) as $desc){
			$lores[] = TextFormat::GRAY . $desc;
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

}