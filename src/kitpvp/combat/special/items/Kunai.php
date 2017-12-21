<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Throwable;

class Kunai extends Throwable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FEATHER, $meta, "Kunai");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Drag your enemies towards you, scorpion style.";
	}

}