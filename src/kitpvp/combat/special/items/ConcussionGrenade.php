<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Throwable;

class ConcussionGrenade extends Throwable{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(384, $meta, "Concussion Grenade");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Blinds and slows down opponents within 5 blocks. Easy escape route.";
	}

}