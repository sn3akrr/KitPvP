<?php namespace kitpvp\kits;

use pocketmine\Server;

use kitpvp\kits\event\KitUnequipEvent;

use core\stats\User;

class Session{

	public $user;
	public $player;
	public $xuid;

	public $activekit = null;
	public $ability = [];

	public $freePlays = [
		"witch" => 0,
		"spy" => 0,
		"scout" => 0,
		"assault" => 0,

		"medic" => 0,
		"archer" => 0,
		"enderman" => 0,
		"pyromancer" => 0,
		"m4l0ne23" => 0,
	];

	public function __construct($user){
		$this->user = new User($user);
		$this->player = $this->user->getPlayer();
		$this->xuid = $this->user->getXuid();

		$this->load();
	}

	public function load(){

	}

	public function getUser(){
		return $this->user;
	}

	public function getPlayer(){
		return $this->player;
	}

	public function getXuid(){
		return $this->xuid;
	}

	public function hasKit(){
		return $this->activeKit != null;
	}

	public function getKit(){
		return $this->activeKit ?? null;
	}

	public function addKit(KitObject $kit){
		$this->activeKit = $kit;
	}

	public function removeKit(){
		$this->activeKit = null;
		Server::getInstance()->getPluginManager()->callEvent(new KitUnequipEvent($this->getPlayer()));
	}

	public function getFreePlays($kit){
		if($kit instanceof KitObject) $kit = $kit->getName();
		return $this->freePlays[$kit] ?? 0;
	}

	public function addFreePlays($kit, $amount = 1){
		if($kit instanceof KitObject) $kit = $kit->getName();
		$this->freePlays[$kit] += $amount;
	}

	public function takeFreePlays($kit, $amount = 1){
		if($kit instanceof KitObject) $kit = $kit->getName();
		$this->freePlays[$kit] -= $amount;
	}

	public function save(){

	}

}