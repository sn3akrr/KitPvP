<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class MaloneSword extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::DIAMOND_SWORD, $meta, "M4L0NESWORD");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "The ultamite sword, named after AvengeTech's longtime owner. Has a change to deal fire and wither effects.";
	}

}