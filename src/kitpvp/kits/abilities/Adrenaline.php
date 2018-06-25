<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\{
	Effect,
	EffectInstance
};

class Adrenaline extends Ability{

	public function __construct(){
		parent::__construct(
			"adrenaline",
			"Gives high speed, jump boost, and restores 7.5 hearts when low on health.",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->addEffect(new EffectInstance(Effect::getEffect(Effect::JUMP), 20 * 10, 2));
		$player->addEffect(new EffectInstance(Effect::getEffect(Effect::SPEED), 20 * 10, 4));
		$player->setHealth($player->getHealth() + 15);

		parent::activate($player, $target);
	}

}