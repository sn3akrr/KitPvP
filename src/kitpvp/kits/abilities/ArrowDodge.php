<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\level\sound\EndermanTeleportSound;

class ArrowDodge extends Ability{

	public function __construct(){
		parent::__construct(
			"arrow dodge",
			"25% chance of arrows doing no damage"
		);
	}

	public function activate(Player $player, $target = null){
		$target->setCancelled(true);
		$player->getLevel()->addSound(new EndermanTeleportSound($player));
	}

}