<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\level\particle\FlameParticle;
use pocketmine\entity\Living;

use kitpvp\KitPvP;

class FireAura extends Ability{

	public function __construct(){
		parent::__construct(
			"fire aura",
			"Automatically attack nearby enemies",
			true, -1, 100, false, true
		);
	}

	public function tick(){
		$player = $this->player;
		$dmg = false;			
		foreach($player->getLevel()->getEntities() as $p){
			if($p != $player && $p instanceof Living && $player->distance($p) < 6 && (!$p instanceof Player || !$teams->sameTeam($player, $p))){
				if($p->getHealth() - 2 <= 0){}else{
					$dmg = true;
					KitPvP::getInstance()->getCombat()->getSlay()->damageAs($player, $p, 2);
					for($i = 0; $i <= 5; $i++){
						$p->getLevel()->addParticle(new FlameParticle($p->add((mt_rand(-10,10)/10),(mt_rand(0,20)/10),(mt_rand(-10,10)/10))));
					}
				}
			}
		}
		if($dmg){
			for($i = 0; $i <= 5; $i++){
				$player->getLevel()->addParticle(new FlameParticle($player->add((mt_rand(-10,10)/10),(mt_rand(0,20)/10),(mt_rand(-10,10)/10))));
			}
		}
	}

}