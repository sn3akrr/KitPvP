<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

class LifeSteal extends Ability{

	public function __construct(){
		parent::__construct(
			"life steal",
			"Gain health when killing players"
		);
	}

	public function activate(Player $player, $target = null){
		$player->setHealth($player->getMaxHealth());
		parent::activate($player, $target);
	}

}