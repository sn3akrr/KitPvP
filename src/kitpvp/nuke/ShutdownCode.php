<?php namespace kitpvp\nuke;

use pocketmine\item\Item;

class ShutdownCode extends Item{

	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::PAPER, $meta, "Shutdown Code");
		$this->setCount($count);
	}

}