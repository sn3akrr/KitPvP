<?php namespace kitpvp\combat\special\items;

class Syringe extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::ARROW, $meta, "Syringe");
		$this->setCount($count);
	}

	public function isConsumable(){
		return true;
	}

}