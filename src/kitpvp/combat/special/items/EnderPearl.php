<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Throwable;

class EnderPearl extends Throwable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(368, $meta, "Ender Pearl");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Easily escape from enemy attacks. Teleports you to impact location.";
	}

}