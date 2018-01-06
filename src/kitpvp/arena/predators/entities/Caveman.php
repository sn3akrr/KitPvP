<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;
use pocketmine\item\Item;

class Caveman extends Predator{

	public $attackDamage = 4;
	public $speed = 0.35;
	public $startingHealth = 25;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/caveman.dat")));
	}

	public function getType(){
		return "Caveman";
	}

	public function getDrops() : array{
		if(mt_rand(0,5) == 0){
			return [Item::get(Item::COOKED_CHICKEN, 0, mt_rand(1,3))];
		}
		return [];
	}

}