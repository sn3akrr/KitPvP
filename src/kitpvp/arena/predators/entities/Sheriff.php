<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;

class Sheriff extends Boss{

	public $attackDamage = 8;
	public $speed = 0.5;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/sheriffboss.dat")));
	}

	public function getType(){
		return "Sheriff";
	}

	public function getReinforcement(Level $level, CompoundTag $nbt){
		if(mt_rand(0,1) == 0){
			return new Cowboy($level, $nbt);
		}else{
			return new Bandit($level, $nbt);
		}
	}

}