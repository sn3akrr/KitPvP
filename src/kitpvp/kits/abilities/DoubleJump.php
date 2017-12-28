<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;
use pocketmine\level\sound\GhastShootSound;

class DoubleJump extends Ability{

	const JUMP_COOLDOWN = 5;

	public $lastJump = 0;
	public $canJump = true;

	public function __construct(){
		parent::__construct(
			"double jump",
			"Press the jump button twice to leap farther",
			true, -1, 2, false, true
		);
	}

	public function tick(){
		$player = $this->player;
		if($this->canJump){
			if($player->isFlying()){
				$player->setGamemode(1); $player->setGamemode(0);
				$dv = $player->getDirectionVector();
				$player->knockback($player, 0, $dv->x, $dv->z, 0.7);
				$player->getLevel()->addSound(new GhastShootSound($player));

				$this->lastJump = time();
				$this->canJump = false;
			}
		}else{
			if(time() - $this->lastJump >= self::JUMP_COOLDOWN){
				$player->setAllowFlight(true);
				$this->canJump = true;
			}else{
				$player->sendTip("Double jump recharging");
			}
		}
		parent::tick();
	}

	public function activate(Player $player, $target = null){
		$player->setAllowFlight(true);
		parent::activate($player, $target);
	}

	public function deactivate(){
		$this->player->setAllowFlight(false);
		$this->player->setGamemode(1); $this->player->setGamemode(0);
		parent::deactivate();
	}

}