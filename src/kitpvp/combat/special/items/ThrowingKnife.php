<?php namespace kitpvp\combat\special\items;

class ThrowingKnife extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FEATHER, $meta, $count, "Throwing Knife");
	}

	public function isConsumable(){
		return true;
	}

}