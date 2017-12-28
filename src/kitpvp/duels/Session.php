<?php namespace kitpvp\duels;

use pocketmine\Player;

use kitpvp\KitPvP;

use core\stats\User;

class Session{

	public $user;
	public $player;
	public $xuid;

	public $wins = 0;
	public $losses = 0;
	public $draws = 0;

	public $preferredArena = null;

	public function __construct($user){
		$this->user = new User($user);
		$this->player = $this->user->getPlayer();
		$this->xuid = $this->user->getXuid();

		$this->load();
	}

	public function load(){
		$xuid = $this->getXuid();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("SELECT wins, losses, draws FROM duels_stats WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_result($wins, $losses, $draws);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();

		$this->wins = (int) $wins;
		$this->losses = (int) $losses;
		$this->draws = (int) $draws;
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

	public function getWins(){
		return $this->wins;
	}

	public function addWin(){
		$this->wins++;
	}

	public function getLosses(){
		return $this->losses;
	}

	public function addLoss(){
		$this->losses++;
	}

	public function getDraws(){
		return $this->draws;
	}

	public function addDraw(){
		$this->draws++;
	}

	public function hasPreferredArena(){
		return $this->getPreferredArena() != "";
	}

	public function getPreferredArena(){
		return $this->preferredArena;
	}

	public function setPreferredArena($id = null){
		$this->preferredArena = $id;
		foreach(KitPvP::getInstance()->getDuels()->getQueues() as $queue){
			if($queue->inQueue($this->getPlayer())){
				$queue->addPlayer($this->getPlayer(), $id);
			}
		}
	}

	public function save(){
		$xuid = $this->getXuid();
		$wins = $this->getWins();
		$losses = $this->getLosses();
		$draws = $this->getDraws();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO duels_stats(xuid, wins, losses, draws) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE wins=VALUES(wins), losses=VALUES(losses), draws=VALUES(draws)");
		$stmt->bind_param("iii", $xuid, $wins, $losses, $draws);
		$stmt->execute();
		$stmt->close();
	}

}