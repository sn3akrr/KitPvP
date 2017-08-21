<?php namespace kitpvp\combat\special\items;

class Flamethrower extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(418, $meta, $count, "Flamethrower");
	}

}