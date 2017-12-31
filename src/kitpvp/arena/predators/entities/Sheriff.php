<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;

class Sheriff extends Predator{

	public $attackDamage = 6;
	public $speed = 0.5;
	public $startingHealth = 100;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/sheriffboss.dat")));
	}

	public function getType(){
		return "Sheriff";
	}

	public function isBoss(){
		return true;
	}

}