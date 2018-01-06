<?php namespace kitpvp\kits;

use pocketmine\Server;

use kitpvp\KitPvP;
use kitpvp\kits\event\KitUnequipEvent;

use core\stats\User;

class Session{

	public $user;

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

	public $kitUsage = [
		"noob" => 0,
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
		$this->load();
	}

	public function load(){
		$xuid = $this->getXuid();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("SELECT witch, spy, scout, assault, medic, archer, enderman, pyromancer, m4l0ne23 FROM kits_freeplays WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($witch, $spy, $scout, $assault, $medic, $archer, $enderman, $pyromancer, $malone);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();

		if($witch == null) return;

		$this->freePlays = [
			"witch" => $witch,
			"spy" => $spy,
			"scout" => $scout,
			"assault" => $assault,

			"medic" => $medic,
			"archer" => $archer,
			"enderman" => $enderman,
			"pyromancer" => $pyromancer,
			"m4l0ne23" => $malone
		];

		$stmt = $db->prepare("SELECT noob, witch, spy, scout, assault, medic, archer, enderman, pyromancer, m4l0ne23 FROM kits_usage WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($noob, $witch, $spy, $scout, $assault, $medic, $archer, $enderman, $pyromancer, $malone);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();

		if($noob == null) return;

		$this->kitUsage = [
			"noob" => $noob,
			"witch" => $witch,
			"spy" => $spy,
			"scout" => $scout,
			"assault" => $assault,

			"medic" => $medic,
			"archer" => $archer,
			"enderman" => $enderman,
			"pyromancer" => $pyromancer,
			"m4l0ne23" => $malone
		];
	}

	public function getUser(){
		return $this->user;
	}

	public function getPlayer(){
		return $this->getUser()->getPlayer();
	}

	public function getXuid(){
		return $this->getUser()->getXuid();
	}

	public function hasKit(){
		return $this->activeKit != null;
	}

	public function getKit(){
		return $this->activeKit ?? null;
	}

	public function addKit(KitObject $kit){
		$this->activeKit = $kit;
		$this->addKitUse($kit->getName());
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

		$usage = $this->getKitUsage($kit->getName());
		if($usage >= 10) if(!$as->hasAchievement($kit->getName() . "_use_1")) $as->get($kit->getName() . "_use_1");
		if($usage >= 25) if(!$as->hasAchievement($kit->getName() . "_use_2")) $as->get($kit->getName() . "_use_2");
		if($usage >= 100) if(!$as->hasAchievement($kit->getName() . "_use_3")) $as->get($kit->getName() . "_use_3");
	}

	public function removeKit(){
		$kit = $this->getKit();
		if($kit == null) return;

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

	public function getKitUsage($kit){
		return $this->kitUsage[$kit] ?? 0;
	}

	public function addKitUse($kit){
		$this->kitUsage[$kit] += 1;
	}

	public function getMostUsedKit(){
		$uses = 0;
		$kit = "";

		foreach($this->kitUsage as $kitt => $use){
			if($use > $uses){
				$uses = $use;
				$kit = $kitt;
			}
		}
		return $kit;
	}

	public function save(){
		$xuid = $this->getXuid();
		$witch = $this->getFreePlays("witch");
		$spy = $this->getFreePlays("spy");
		$scout = $this->getFreePlays("scout");
		$assault = $this->getFreePlays("assault");
		$medic = $this->getFreePlays("medic");
		$archer = $this->getFreePlays("archer");
		$enderman = $this->getFreePlays("enderman");
		$pyromancer = $this->getFreePlays("pyromancer");
		$malone = $this->getFreePlays("m4l0ne23");

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO kits_freeplays(xuid, witch, spy, scout, assault, medic, archer, enderman, pyromancer, m4l0ne23) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE witch=VALUES(witch), spy=VALUES(spy), scout=VALUES(scout), assault=VALUES(assault), medic=VALUES(medic), archer=VALUES(archer), enderman=VALUES(enderman), pyromancer=VALUES(pyromancer), m4l0ne23=VALUES(m4l0ne23)");
		$stmt->bind_param("iiiiiiiiii", $xuid, $witch, $spy, $scout, $assault, $medic, $archer, $enderman, $pyromancer, $malone);
		$stmt->execute();
		$stmt->close();

		$noob = $this->getKitUsage("noob");
		$witch = $this->getKitUsage("witch");
		$spy = $this->getKitUsage("spy");
		$scout = $this->getKitUsage("scout");
		$assault = $this->getKitUsage("assault");
		$medic = $this->getKitUsage("medic");
		$archer = $this->getKitUsage("archer");
		$enderman = $this->getKitUsage("enderman");
		$pyromancer = $this->getKitUsage("pyromancer");
		$malone = $this->getKitUsage("m4l0ne23");

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO kits_usage(xuid, noob, witch, spy, scout, assault, medic, archer, enderman, pyromancer, m4l0ne23) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE witch=VALUES(witch), spy=VALUES(spy), scout=VALUES(scout), assault=VALUES(assault), medic=VALUES(medic), archer=VALUES(archer), enderman=VALUES(enderman), pyromancer=VALUES(pyromancer), m4l0ne23=VALUES(m4l0ne23)");
		$stmt->bind_param("iiiiiiiiiii", $xuid, $noob, $witch, $spy, $scout, $assault, $medic, $archer, $enderman, $pyromancer, $malone);
		$stmt->execute();
		$stmt->close();
	}

}