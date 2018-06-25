<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\{
	Effect,
	EffectInstance,
	Living
};
use pocketmine\level\sound\EndermanTeleportSound;

use kitpvp\KitPvP;

class Slender extends Ability{

	public function __construct(){
		parent::__construct(
			"slender",
			"All enemies nearby are blinded when you're low on health, one time use",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 20 * 5));
		$player->getLevel()->addSound(new EndermanTeleportSound($player));
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$spec = KitPvP::getInstance()->getArena()->getSpectate();
		foreach($player->getLevel()->getNearbyEntities($player->getBoundingBox()->expandedCopy(4, 4, 4)) as $p){
			if($p != $player && $p instanceof Living && (!$p instanceof Player || (!$teams->sameTeam($player, $p) && !$spec->isSpectating($p)))){
				$dv = $p->getDirectionVector();
				$p->knockback($p, 0 -$dv->x, -$dv->z, 0.8);
				$p->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20 * 7));
			}
		}
	}

}