<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;
use pocketmine\item\Item;

class King extends Boss{

	public $attackDamage = 8;
	public $speed = 0.6;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setSkin(new Skin("Standard_Custom", file_get_contents("/home/data/skins/kingboss.dat")));
	}

	public function getType(){
		return "King";
	}

	public function getReinforcement(Level $level, CompoundTag $nbt){
		if(mt_rand(0,1) == 0){
			return new Knight($level, $nbt);
		}else{
			return new Pawn($level, $nbt);
		}
	}

	public function getDrops() : array{
		return [Item::get(Item::STEAK, 0, mt_rand(2,4))];

	}

}