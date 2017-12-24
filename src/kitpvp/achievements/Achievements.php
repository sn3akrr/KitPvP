<?php namespace kitpvp\achievements;

use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\achievements\commands\AchCommand;

use core\stats\User;

class Achievements{

	public $plugin;
	public $database;

	public $achievements = [];
	public $sessions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([
			"CREATE TABLE IF NOT EXISTS achievement_data(xuid BIGINT(16) NOT NULL UNIQUE, points INT NOT NULL DEFAULT '0', achievements VARCHAR(30000) NOT NULL)",
		] as $stmt) $this->database->query($stmt);
		$plugin->getServer()->getCommandMap()->register("a", new AchCommand($plugin, "a", "Open your personal achievement menu"));

		$this->setupAchievements();
	}

	public function close(){
		foreach($this->sessions as $name => $session){
			$session->save();
		}
	}

	public function setupAchievements(){
		foreach(AchievementList::ACHIEVEMENTS as $id => $data){
			$this->achievements[$id] = new Achievement($id, $data["displayName"], $data["description"], $data["points"]);
		}
	}

	public function getAchievements(){
		return $this->achievements;
	}

	public function getAchievement($id){
		return isset($this->achievements[$id]) ? clone $this->achievements[$id] : null;
	}

	public function getAchievementCount(){
		return count($this->achievements);
	}

	public function createSession(Player $player){
		$this->sessions[$player->getName()] = new Session($player);
	}

	public function getSession(Player $player){
		return $this->sessions[$player->getName()] ?? null;
	}

	public function deleteSession(Player $player){
		$this->sessions[$player->getName()]->save();
		unset($this->sessions[$player->getName()]);
	}

}