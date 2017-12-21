<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class SpikedClub extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_SHOVEL, $meta, "Spiked Club");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Does extra damage. Leaves opponents bleeding. Literally.";
	}

}