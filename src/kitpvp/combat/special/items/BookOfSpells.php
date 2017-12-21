<?php namespace kitpvp\combat\special\items;

use kitpvp\combat\special\items\types\Melee;

class BookOfSpells extends Melee{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::BOOK, $meta, "Book of Spells");
		$this->setCount($count);

		$this->init();
	}

	public function getDescription(){
		return "Cast dangerous spells on nearby players! Tap the ground to cast a spell on all players within 10 blocks.";
	}

}