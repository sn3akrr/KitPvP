<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;

class Cowboy extends Predator{

	public $attackDamage = 3;
	public $speed = 0.45;
	public $startingHealth = 20;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/cowboy.dat")));
	}

	public function getType(){
		return "Cowboy";
	}

}