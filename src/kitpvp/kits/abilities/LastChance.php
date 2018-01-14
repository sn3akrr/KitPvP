<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\entity\Effect;

use kitpvp\KitPvP;

class LastChance extends Ability{

	public function __construct(){
		parent::__construct(
			"last chance",
			"Knocks back players and 5 second invisibility when low on health",
			false, -1, 5, false
		);
	}

	public function activate(Player $player, $target = null){
		$player->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 5));
		$player->addEffect(Effect::getEffect(Effect::INVISIBILITY)->setDuration(20 * 5));
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$spec = KitPvP::getInstance()->getArena()->getSpectate();
		foreach($player->getLevel()->getNearbyEntities($player->getBoundingBox()->grow(4, 4, 4)) as $p){
			if($p != $player && $p instanceof Living && (!$p instanceof Player || (!$teams->sameTeam($player, $p) && !$spec->isSpectating($p)))){
				$dv = $p->getDirectionVector();
				$p->knockback($p, 0 -$dv->x, -$dv->z, 0.8);
			}
		}
	}

}