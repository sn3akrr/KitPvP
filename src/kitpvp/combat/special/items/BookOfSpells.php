<?php namespace kitpvp\combat\special\items;

class BookOfSpells extends SpecialWeapon{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::BOOK, $meta, "Book of Spells");
		$this->setCount($count);
	}

}