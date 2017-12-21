<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Shootable;

class Flamethrower extends Shootable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(418, $meta, "Flamethrower");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Launch scortching hot flames at your enemies.";
	}

}