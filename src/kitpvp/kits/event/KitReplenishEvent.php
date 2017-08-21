<?php namespace kitpvp\kits\event;

use kitpvp\kits\KitObject as Kit;
use core\AtPlayer as Player;

class KitReplenishEvent extends KitEvent{

	public static $handlerList = null;

	private $player;
	private $kit;

	public function __construct(Player $player, Kit $kit){
		$this->player = $player;
		$this->kit = $kit;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getKit(){
		return $this->kit;
	}

}