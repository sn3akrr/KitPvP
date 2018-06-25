<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\{
	Effect,
	EffectInstance
};

class Curse extends Ability{

	public function __construct(){
		parent::__construct(
			"curse",
			"5% chance to poison your attackers"
		);
	}

	public function activate(Player $player, $target = null){
		if($target == null) return;
		$target->addEffect(new EffectInstance(Effect::getEffect(Effect::POISON), 20 * 4, 2));
		parent::activate($player, $target);
	}

}