<?php namespace kitpvp\arena\envoys\pickups;

use pocketmine\item\Item;
use pocketmine\effect\Effect;

class EffectPickup extends Item{

	public $effect;

	public function __construct($count = 1, Effect $effect){
		parent::__construct(self::SLIMEBALL, 0, "Effect Pickup");
		$this->setCount($count);

		$this->effect = $effect;
	}

	public function getEffect(){
		return $this->effect;
	}

}