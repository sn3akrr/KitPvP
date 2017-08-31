<?php namespace kitpvp\combat\special\items;

class Defibrillator extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SHEARS, $meta, "Defibrillator");
		$this->setCount($count);
	}

}