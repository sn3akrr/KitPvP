<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

use kitpvp\KitPvP;

class AimAssist extends Ability{

	const MAX_DISTANCE = 20;
	public $target = null;

	public function __construct(){
		parent::__construct(
			"aim assist",
			"Automatically aims on players nearby",
			true, 3
		);
	}

	public function tick(){
		$player = $this->player;
		if($this->target == null){
			$distance = self::MAX_DISTANCE;
			$target = null;
			foreach($player->getViewers() as $p){
				$teams = KitPvP::getInstance()->getCombat()->getTeams();
				if($p != $player && $player->distance($p) <= $distance && (!$teams->sameTeam($player, $p))){
					$distance = $player->distance($p);
					$target = $p;
				}
			}
			$this->target = $target;
			return true;
		}
		$target = $this->target;
		$x = $player->x - $target->x;
		$y = $player->y - $target->y;
		$z = $player->z - $target->z;
		$yaw = asin($x / sqrt($x * $x + $z * $z)) / 3.14 * 180;
		$pitch = round(asin($y / sqrt($x * $x + $z * $z + $y * $y)) / 3.14 * 180);
		if($z > 0) $yaw = -$yaw + 180;
		$player->teleport($player, $yaw, $pitch + 0.25);

		return false;
	}

	public function deactivate(){
		$this->target = null;
		parent::deactivate();
	}

}