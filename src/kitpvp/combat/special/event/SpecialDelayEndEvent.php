<?php namespace kitpvp\combat\special\event;

use pocketmine\Player;

class SpecialDelayEndEvent extends SpecialEvent{

	public static $handlerList = null;

	public $player;
	public $special;

	public function __construct(Player $player, $special){
		$this->player = $player;
		$this->special = $special;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getSpecial(){
		return $this->special;
	}

}