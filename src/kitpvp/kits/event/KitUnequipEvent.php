<?php namespace kitpvp\kits\event;

use core\AtPlayer as Player;

class KitUnequipEvent extends KitEvent{

	public static $handlerList = null;

	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function getPlayer(){
		return $this->player;
	}

}