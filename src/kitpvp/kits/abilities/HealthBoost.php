<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

class HealthBoost extends Ability{

	public function __construct(){
		parent::__construct(
			"health boost",
			"Gain 2 extra hearts in the arena",
			true, -1, 100, false, true
		);
	}

	public function tick(){
		//This ticks so it can be deactivated correctly.
	}

	public function activate(Player $player, $target = null){
		$player->setMaxHealth(24);
		$player->setHealth(24);
		parent::activate($player, $target);
	}

	public function deactivate(){
		$this->player->setMaxHealth(20);
		parent::deactivate();
	}

}