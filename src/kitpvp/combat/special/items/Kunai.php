<?php namespace kitpvp\combat\special\items;

class Kunai extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FEATHER, $meta, "Kunai");
		$this->setCount($count);
	}

	public function isConsumable(){
		return true;
	}

}