<?php namespace kitpvp\arena\envoys;

use pocketmine\Server;

use kitpvp\KitPvP;

use core\utils\SaveableSession;

class Session extends SaveableSession{

	public $collected = 0;

	public function load(){
		$xuid = $this->getXuid();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("SELECT collected FROM envoy_data WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($collected);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();
		if($collected == null) return;

		$this->collected = $collected;
	}

	public function getCollected(){
		return $this->collected;
	}

	public function addCollected(){
		$this->collected++;

		$collected = $this->getCollected();

		$as = KitPvP::getInstance()->getAchievements()->getSession($this->getPlayer());
		if($collected >= 1) if(!$as->hasAchievement("envoy_1")) $as->get("envoy_1");
		if($collected >= 5) if(!$as->hasAchievement("envoy_2")) $as->get("envoy_2");
		if($collected >= 25) if(!$as->hasAchievement("envoy_3")) $as->get("envoy_3");
		if($collected >= 100) if(!$as->hasAchievement("envoy_4")) $as->get("envoy_4");
		if($collected >= 1000) if(!$as->hasAchievement("envoy_5")) $as->get("envoy_5");
	}

	public function save(){
		$xuid = $this->getXuid();
		$collected = $this->getCollected();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO envoy_data(xuid, collected) VALUES(?, ?) ON DUPLICATE KEY UPDATE collected=VALUES(collected)");
		$stmt->bind_param("ii", $xuid, $collected);
		$stmt->execute();
		$stmt->close();
	}

}