<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\Effect;

class Curse extends Ability{

	public function __construct(){
		parent::__construct(
			"curse",
			"5% chance to poison your attackers"
		);
	}

	public function activate(Player $player, $target = null){
		if($target == null) return;
		$target->addEffect(Effect::getEffect(Effect::POISON)->setDuration(20 * 4)->setAmplifier(2));
		parent::activate($player, $target);
	}

}