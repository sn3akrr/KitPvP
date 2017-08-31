<?php namespace kitpvp\combat\special\items;

class Shuriken extends SpecialWeapon{

	public function __construct($meta = 0, $count = 3){
		parent::__construct(self::NETHER_STAR, $meta, "Shuriken");
		$this->setCount($count);
	}

	public function isConsumable(){
		return true;
	}

}