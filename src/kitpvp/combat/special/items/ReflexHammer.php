<?php namespace kitpvp\combat\special\items;

class ReflexHammer extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::STONE_AXE, $meta, $count, "Reflex Hammer");
	}

}