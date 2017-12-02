<?php namespace kitpvp\combat\special\items;

class SpikedClub extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_SHOVEL, $meta, "Spiked Club");
		$this->setCount($count);
	}

}