<?php namespace kitpvp\kits\abilities;

use pocketmine\Player;

use kitpvp\KitPvP;

abstract class Ability{

	public $name;
	public $description;

	public $ticks;
	public $tickCount = 0;
	public $maxTicks = -1;
	public $tickRate = 5;

	public $multipleUses;
	public $used = false;

	public $equipActivate;

	public $active = false;

	public $player = null;

	public function __construct($name, $description, $ticks = false, $maxTicks = -1, $tickRate = 5, $multipleUses = true, $equipActivate = false){
		$this->name = $name;
		$this->description = $description;

		$this->ticks = $ticks;
		$this->maxTicks = $maxTicks;
		$this->tickRate = $tickRate;
		$this->multipleUses = $multipleUses;
		$this->equipActivate = $equipActivate;
	}

	public function getName(){
		return $this->name;
	}

	public function getDescription(){
		return $this->description;
	}

	public function doesTick(){
		return $this->ticks;
	}

	public function getTickCount(){
		return $this->tickCount;
	}

	public function resetTickCount(){
		$this->tickCount = 0;
	}

	public function getMaxTicks(){
		return $this->maxTicks;
	}

	public function getTickRate(){
		return $this->tickRate;
	}

	public function tick(){
		$this->tickCount++;
		if($this->getMaxTicks() != -1){
			if($this->getTickCount() > $this->getMaxTicks()){
				$this->deactivate($this->player);
				return false;
			}
		}
		return true;
		//Only if doesTick() is true
	}

	public function hasMultipleUses(){
		return $this->multipleUses;
	}

	public function setUsed(){
		$this->used = true;
	}

	public function isUsed(){
		return $this->used;
	}

	public function activateOnEquip(){
		return $this->equipActivate;
	}

	public function isActive(){
		return $this->active;
	}

	public function activate(Player $player, $target = null){
		if($this->doesTick()){
			$this->active = true;
			$this->player = $player;
			KitPvP::getInstance()->getKits()->getCorrespondingTicker($this)->addTicking($player);
		}
		if(!$this->hasMultipleUses()) $this->setUsed();
	}

	public function deactivate(){
		$this->active = false;
		$this->player = null;
		$this->resetTickCount();
	}

}