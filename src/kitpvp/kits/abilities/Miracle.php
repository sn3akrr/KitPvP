<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

class Miracle extends Ability{

	public function __construct(){
		parent::__construct(
			"miracle",
			"Regain 2.5 hearts when low on health one time",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->setHealth($player->getHealth() + 5);
		parent::activate($player);
	}

}