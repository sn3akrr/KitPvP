<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class Defibrillator extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::SHEARS, $meta, "Defibrillator");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Electrocutes and stuns your target.";
	}

}