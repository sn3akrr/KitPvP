<?php namespace kitpvp\combat\special\items;

class ConcussionGrenade extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(384, $meta, "Concussion Grenade");
		$this->setCount($count);
	}

	public function isConsumable(){
		return true;
	}

}