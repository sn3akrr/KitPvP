<?php namespace kitpvp\items;

use pocketmine\item\ItemFactory;

class Items{

	public static function init(){
		ItemFactory::registerItem(new CookedMutton());
	}

}