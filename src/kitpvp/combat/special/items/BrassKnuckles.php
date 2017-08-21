<?php namespace kitpvp\combat\special\items;

class BrassKnuckles extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_INGOT, $meta, $count, "Brass Knuckles");
	}

}