<?php namespace kitpvp\combat\special\items;

class Gun extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::FISHING_ROD, $meta, "Gun");
		$this->setCount($count);
	}

}