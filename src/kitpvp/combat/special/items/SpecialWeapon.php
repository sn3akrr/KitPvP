<?php namespace kitpvp\combat\special\items;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\nbt\tag\{
	CompoundTag,
	ListTag,
	FloatTag,
	DoubleTag,
	ShortTag
};

use kitpvp\KitPvP;

class SpecialWeapon extends Item{

	public function isConsumable(){
		return false;
	}

}