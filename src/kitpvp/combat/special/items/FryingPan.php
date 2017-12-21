<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class FryingPan extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::HOPPER, $meta, "Frying Pan");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Whack players with this to deal some serious knockback.";
	}

}