<?php namespace kitpvp\kits;

use pocketmine\Server;

use kitpvp\KitPvP;
use kitpvp\kits\event\KitUnequipEvent;

use core\stats\User;

class Session{

	public $user;
	public $player;
	public $xuid;

	public $activeKit = null;

	public $bowShots = 0;
	public $missedBowShots = 0;

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
		$this->activeKit = clone $kit;
		$as = KitPvP::getInstance()->getAchievements()->getSession($this->getPlayer());
		switch($kit->getName()){
			case "noob":
				if(!$as->hasAchievement("noob_first")) $as->get("noob_first");
			break;
			case "witch":
				if(!$as->hasAchievement("witch_first")) $as->get("witch_first");
			break;
			case "spy":
				if(!$as->hasAchievement("spy_first")) $as->get("spy_first");
			break;
			case "scout":
				if(!$as->hasAchievement("scout_first")) $as->get("scout_first");
			break;
			case "assault":
				if(!$as->hasAchievement("assault_first")) $as->get("assault_first");
			break;
			case "medic":
				if(!$as->hasAchievement("medic_first")) $as->get("medic_first");
			break;
			case "archer":
				if(!$as->hasAchievement("archer_first")) $as->get("archer_first");
			break;
			case "enderman":
				if(!$as->hasAchievement("enderman_first")) $as->get("enderman_first");
			break;
			case "pyromancer":
				if(!$as->hasAchievement("pyromancer_first")) $as->get("pyromancer_first");
			break;
			case "m4l0ne23":
				if(!$as->hasAchievement("malone_first")) $as->get("malone_first");
			break;
		}
	}

	public function removeKit(){
		$kit = $this->getKit();
		foreach($kit->getAbilities() as $ability){
			if($ability->isActive()) $ability->deactivate($this->getPlayer());
		}
		$this->activeKit = null;
		Server::getInstance()->getPluginManager()->callEvent(new KitUnequipEvent($this->getPlayer()));
	}

	public function getBowShots(){
		return $this->bowShots;
	}

	public function addBowShot(){
		$this->bowShots++;
		$this->addMissedBowShot();
	}

	public function resetBowShots(){
		$this->bowShots = 0;
		$this->resetMissedBowShots();
	}

	public function getMissedBowShots(){
		return $this->missedBowShots;
	}

	public function addMissedBowShot(){
		$this->missedBowShots++;
	}

	public function resetMissedBowShots(){
		$this->missedBowShots = 0;
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