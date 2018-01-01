<?php namespace kitpvp\arena\predators;

use pocketmine\Server;

use kitpvp\KitPvP;

use core\stats\User;

class Session{

	public $user;
	public $player;
	public $xuid;

	public function __construct($user){
		$this->user = new User($user);
		$this->player = $this->user->getPlayer();
		$this->xuid = $this->user->getXuid();

		$this->load();
	}

	public function load(){

	}

	public function save(){

	}

}