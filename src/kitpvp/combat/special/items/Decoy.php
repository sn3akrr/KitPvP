<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Throwable;

class Decoy extends Throwable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(332, $meta, "Decoy");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Turn completely invisible, armor and all, for 3 seconds. Easy escape route.";
	}

}