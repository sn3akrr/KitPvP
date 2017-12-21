<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class FireAxe extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_AXE, $meta, "Fire Axe");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Burns opponents, deals extra damage.";
	}

}