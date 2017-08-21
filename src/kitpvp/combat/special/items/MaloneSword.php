<?php namespace kitpvp\combat\special\items;

class MaloneSword extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_SWORD, $meta, $count, "M4L0NESWORD");
	}

}