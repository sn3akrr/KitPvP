<?php namespace kitpvp\techits;

/**
 * Area used for server-specific techit saving.
 * Probably looks bad but hey it works..
 */

use kitpvp\KitPvP;

use core\utils\SaveableSession;

class Session extends SaveableSession{

	public $techits = 0;

	public function load(){
		$db = KitPvP::getInstance()->database;
		$xuid = $this->getXuid();

		$stmt = $db->prepare("SELECT techits FROM techits WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($techits);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();

		if($techits == null) return;

		$this->techits = $techits;
	}

	public function getTechits(){
		return $this->techits;
	}

	public function addTechits($value = 1){
		$this->techits += $value;
	}

	public function takeTechits($value = 1){
		$this->techits -= $value;
		if($this->techits < 0) $this->techits = 0;
	}

	public function save(){
		$db = KitPvP::getInstance()->database;
		$xuid = $this->getXuid();
		$techits = $this->getTechits();

		$stmt = $db->prepare("INSERT INTO techits(xuid, techits) VALUES(?, ?) ON DUPLICATE KEY UPDATE techits=VALUES(techits)");
		$stmt->bind_param("ii", $xuid, $techits);
		$stmt->execute();
		$stmt->close();
	}

}