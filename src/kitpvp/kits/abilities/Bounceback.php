<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

class Bounceback extends Ability{

	public function __construct(){
		parent::__construct(
			"bounceback",
			"25% chance to knockback your attackers"
		);
	}

	public function activate(Player $player, $target = null){
		$dv = $target->getDirectionVector();
		$target->knockback($target, 0 -$dv->x, -$dv->z, 0.45);
	}

}