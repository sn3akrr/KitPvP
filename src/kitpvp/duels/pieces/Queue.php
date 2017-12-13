<?php namespace kitpvp\duels\pieces;

use pocketmine\{
	Player,
	Server
};

use kitpvp\KitPvP;

class Queue{

	public $id;
	public $name;

	public $players = [];

	public function __construct($id, $name){
		$this->id = $id;
		$this->name = $name;
	}

	public function tick(){
		$matches = [];
		foreach($this->players as $name => $pref){
			$player = Server::getInstance()->getPlayerExact($name);
			if($player instanceof Player){
				if(!isset($matches[$pref])) $matches[$pref] = [];
				$player->sendPopup("Waiting in duel queue...");
				$matches[$pref][] = $player;
			}else{
				unset($this->players[$name]);
			}
		}

		$duels = KitPvP::getInstance()->getDuels();

		//First cycle. Get's all currently available pairs.
		foreach($matches as $arena => $players){
			while(count($matches[$arena]) > 1){
				$player1 = array_shift($matches[$arena]);
				$player2 = array_shift($matches[$arena]);
				$duels->createDuel($player1, $player2, ($arena == "none" ? null : $arena));
				unset($this->players[$player1->getName()]);
				unset($this->players[$player2->getName()]);
			}
		}

		foreach($matches as $arena => $players){
			if(empty($matches[$arena])) unset($matches[$arena]);
		}

		if(isset($matches["none"]) && count($matches["none"]) > 0){
			//Second cycle. Pairs extra player with no preferred arena with others with preferred maps
			$player1 = array_shift($matches["none"]);
			unset($matches["none"]);
			foreach($matches as $arena => $players){
				$player2 = array_shift($matches[$arena]);
				$duels->createDuel($player1, $player2, $arena);
				unset($player1);
				unset($this->players[$player1->getName()]);
				unset($this->players[$player2->getName()]);
				break;
			}
			if(isset($player1)){
				$matches["none"] = $player1;
			}
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getPlayers(){
		return $this->players;
	}

	public function addPlayer(Player $player, $pm = null){
		$this->players[$player->getName()] = ($pm == null ? "none" : $pm);
	}

	public function removePlayer(Player $player){
		unset($this->players[$player->getName()]);
	}

	public function inQueue(Player $player){
		return isset($this->players[$player->getName()]);
	}

}