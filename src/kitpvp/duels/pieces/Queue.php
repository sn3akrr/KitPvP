<?php namespace kitpvp\duels\pieces;

use pocketmine\utils\TextFormat;
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
		$duels = KitPvP::getInstance()->getDuels();

		$nopref = [];
		$wpref = [];

		foreach($this->players as $name => $arena){
			$player = Server::getInstance()->getPlayerExact($name);
			if($player instanceof Player){
				if($arena == "none"){
					$nopref[] = $player;
					$player->sendPopup(TextFormat::YELLOW . "Duel: " . TextFormat::GRAY . "In queue... " . TextFormat::AQUA . $this->getName());
				}else{
					if(!isset($wpref[$arena])) $wpref[$arena] = [];
					$wpref[$arena][] = $player;
					$player->sendPopup(TextFormat::YELLOW . "Duel: " . TextFormat::GRAY . "In queue. " . TextFormat::AQUA . $this->getName() . PHP_EOL . TextFormat::GRAY . "Preferred map: " . TextFormat::LIGHT_PURPLE . $duels->getArena($arena)->getName());
				}
			}else{
				unset($this->players[$name]);
			}
		}

		if(count($nopref) > 1){
			while(count($nopref) > 1){
				$p1 = array_shift($nopref);
				$p2 = array_shift($nopref);

				$duels->createDuel($p1, $p2);

				$this->removePlayer($p1);
				$this->removePlayer($p2);
			}
		}

		foreach($wpref as $arena => $players){
			if(count($wpref[$arena]) > 1){
				while(count($wpref[$arena]) > 1){
					$p1 = array_shift($wpref[$arena]);
					$p2 = array_shift($wpref[$arena]);

					$duels->createDuel($p1, $p2, $arena);

					$this->removePlayer($p1);
					$this->removePlayer($p2);
				}
			}
			if(empty($wpref[$arena])) unset($wpref[$arena]);
		}

		if(!empty($nopref) && !empty($wpref)){
			$p1 = array_shift($nopref);
			foreach($wpref as $arena => $players){
				$p2 = array_shift($wpref[$arena]);

				$duels->createDuel($p1, $p2, $arena);

				$this->removePlayer($p1);
				$this->removePlayer($p2);
				break;
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