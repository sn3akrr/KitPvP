<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Shootable;

class Gun extends Shootable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FISHING_ROD, $meta, "Gun");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Showdown! Easily shoot your enemies to death. Deals major damage.";
	}

}