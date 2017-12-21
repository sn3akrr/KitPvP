<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class BrassKnuckles extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::GOLD_INGOT, $meta, "Brass Knuckles");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Hit players to deal some serious knockback.";
	}

}