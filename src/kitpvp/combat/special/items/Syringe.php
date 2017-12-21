<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\ConsumableMelee;

class Syringe extends ConsumableMelee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::ARROW, $meta, "Syringe");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Poisons and nauseates opponents. 'Ouch! What was in that, doc?'";
	}

}