<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;

class Robot extends Predator{

	public $attackDamage = 5;
	public $speed = 0.4;
	public $startingHealth = 20;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/robot.dat")));
	}

	public function getType(){
		return "Robot";
	}

}