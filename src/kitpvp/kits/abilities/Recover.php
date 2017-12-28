<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

class Recover extends Ability{

	public function __construct(){
		parent::__construct(
			"recover",
			"Slowly regenerate health over time",
			true, -1, 100, false, true
		);
	}

	public function tick(){
		$player = $this->player;
		if($player->getHealth() < $player->getMaxHealth()) $player->setHealth($player->getHealth() + 2);
		parent::tick();
	}

}