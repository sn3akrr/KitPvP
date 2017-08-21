<?php namespace kitpvp\combat\special\items;

class ConcussionGrenade extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(384, $meta, $count, "Concussion Grenade");
	}

	public function isConsumable(){
		return true;
	}

}