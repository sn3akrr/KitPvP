<?php namespace kitpvp\combat\special\items;

class EnderPearl extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(368, $meta, $count, "Ender Pearl");
	}

	public function isConsumable(){
		return true;
	}

}