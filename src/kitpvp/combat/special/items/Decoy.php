<?php namespace kitpvp\combat\special\items;

class Decoy extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(332, $meta, "Decoy");
		$this->setCount($count);
	}

	public function isConsumable(){
		return true;
	}

}