<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\Effect;
use pocketmine\level\sound\EndermanTeleportSound;

class Slender extends Ability{

	public function __construct(){
		parent::__construct(
			"slender",
			"All enemies nearby are blinded when you're low on health, one time use",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->addEffect(Effect::getEffect(Effect::INVISIBILITY)->setDuration(20 * 5));
		$player->getLevel()->addSound(new EndermanTeleportSound($player));
		foreach($player->getViewers() as $p){
			if($p->distance($player) <= 4 && $p != $player){
				$dv = $p->getDirectionVector();
				$p->knockback($p, 0 -$dv->x, -$dv->z, 0.8);
				$p->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 7));
			}
		}
	}

}