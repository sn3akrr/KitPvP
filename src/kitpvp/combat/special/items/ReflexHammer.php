<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class ReflexHammer extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_AXE, $meta, "Reflex Hammer");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Violently check your opponent's reflexes' with this deadly hammer";
	}

}