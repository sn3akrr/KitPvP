<?php namespace kitpvp\combat\special\items;

class FireAxe extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::IRON_AXE, $meta, "Fire Axe");
		$this->setCount($count);
	}

}