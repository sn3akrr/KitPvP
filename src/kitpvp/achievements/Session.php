<?php namespace kitpvp\achievements;

use pocketmine\Server;
use pocketmine\utils\TextFormat;

use core\stats\User;

use kitpvp\KitPvP;

class Session{

	public $user;
	public $xuid;

	public $points = 0;
	public $achievements = [];

	public function __construct($user){
		$user = new User($user);
		$this->user = $user;
		$this->xuid = $user->getXuid();

		$this->load();
	}

	public function load(){
		$xuid = $this->getXuid();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("SELECT points, achievements FROM achievement_data WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($points, $data);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();
		if($points == null) return;
		$this->points = $points;
		$this->achievements = $this->decodeData($data);
	}

	public function getUser(){
		return $this->user;
	}

	public function getXuid(){
		return $this->xuid;
	}

	public function getPoints(){
		return $this->points;
	}

	public function addPoints($points){
		$this->points += $points;
	}

	public function takePoints($points){
		$this->points -= $points;
	}

	public function getAchievements(){
		return $this->achievements;
	}

	public function getAchievement($id){
		return $this->achievements[$id] ?? null;
	}

	public function hasAchievement($id){
		return isset($this->achievements[$id]);
	}

	public function addAchievement($id){
		$achievement = KitPvP::getInstance()->getAchievements()->getAchievement($id);
		if($achievement != null && !$this->hasAchievement($achievement->getId())){
			$achievement->setObtained();
			$this->achievements[$achievement->getId()] = $achievement;
		}
	}

	public function getAchievementCount(){
		return count($this->achievements);
	}

	public function decodeData($data){
		return unserialize(base64_decode($data));
	}

	public function encodeData($data){
		return base64_encode(serialize($data));
	}

	public function get($id){
		$this->addAchievement($id);
		$achievement = $this->getAchievement($id);
		$this->addPoints($achievement->getPoints());

		$player = $this->getUser()->getPlayer();
		if($this->getUser()->validPlayer()){
			$player = $this->getUser()->getPlayer();
			$player->sendMessage(TextFormat::AQUA . "= = = = = = = = = = = =");
			$player->sendMessage(TextFormat::YELLOW . TextFormat::OBFUSCATED . "||" . TextFormat::RESET . TextFormat::GRAY . " Achievement get! (" . TextFormat::YELLOW . $achievement->getName() . TextFormat::GRAY . ") " . TextFormat::YELLOW . TextFormat::OBFUSCATED . "||");
			$player->sendMessage(TextFormat::LIGHT_PURPLE . "+" . $achievement->getPoints() . " achievement points");
			$player->sendMessage(TextFormat::AQUA . "= = = = = = = = = = = =");
		}
	}

	public function save(){
		$xuid = $this->getXuid();
		$points = $this->getPoints();
		$data = $this->encodeData($this->getAchievements());

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO achievement_data(xuid, points, achievements) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE points=VALUES(points), achievements=VALUES(achievements)");
		$stmt->bind_param("iis", $xuid, $points, $data);
		$stmt->execute();
		$stmt->close();
	}

}