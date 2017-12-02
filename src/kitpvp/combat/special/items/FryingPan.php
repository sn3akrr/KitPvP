<?php namespace kitpvp\combat\special\items;

class FryingPan extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::HOPPER, $meta, "Frying Pan");
		$this->setCount($count);
	}

}