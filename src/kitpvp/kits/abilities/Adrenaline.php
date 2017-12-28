<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\Effect;

class Adrenaline extends Ability{

	public function __construct(){
		parent::__construct(
			"adrenaline",
			"Gives high speed, jump boost, and restores 7.5 hearts when low on health.",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->addEffect(Effect::getEffect(Effect::JUMP)->setAmplifier(2)->setDuration(20 * 10));
		$player->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(4)->setDuration(20 * 10));
		$player->setHealth($player->getHealth() + 15);

		parent::activate($player, $target);
	}

}