<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

use kitpvp\KitPvP;

class StealthMode extends Ability{

	const NEEDED_STILL_TICKS = 12;

	public $stillPos = null;
	public $stillTicks = 0;
	public $invisible = false;

	public function __construct(){
		parent::__construct(
			"stealth mode",
			"Invisibility when holding still or sneaking",
			true, -1, 5, false, true
		);
	}

	public function tick(){
		$player = $this->player;
		if(!$this->invisible){
			if($player->getX() == $this->stillPos->getX() && $player->getZ() == $this->stillPos->getZ()){
				$this->stillTicks++;
				if($this->stillTicks == self::NEEDED_STILL_TICKS){
					$this->invisible = true;
					KitPvP::getInstance()->getKits()->setInvisible($player, true);
				}
			}else{
				$this->stillTicks = 0;
				$this->stillPos = $player->asVector3();
			}
			if($player->isSneaking()){
				$this->invisible = true;
				KitPvP::getInstance()->getKits()->setInvisible($player, true);
			}
		}else{
			if($player->getX() != $this->stillPos->getX() || $player->getZ() != $this->stillPos->getZ()){
				$this->stillTicks = 0;
				if(!$player->isSneaking()){
					$this->invisible = false;
					$this->stillPos = $player->asVector3();
					KitPvP::getInstance()->getKits()->setInvisible($player, false);
				}
			}
		}
		parent::tick();
	}

	public function activate(Player $player, $target = null){
		$this->stillPos = $player->asVector3();
		parent::activate($player, $target);
	}

}