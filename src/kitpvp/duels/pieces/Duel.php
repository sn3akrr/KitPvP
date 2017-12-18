<?php namespace kitpvp\duels\pieces;

use pocketmine\{
	Player,
	Server
};
use pocketmine\utils\TextFormat;
use pocketmine\level\Position;

use kitpvp\KitPvP;
use kitpvp\duels\uis\DuelStatsUi;

class Duel{

	const GAME_PREPARE = 0;
	const GAME_FIGHT = 1;

	public $id;
	public $players = [];
	public $arena;

	public $prepare_time;
	public $fight_time;

	public $timer;

	public $status = self::GAME_PREPARE;

	public $winner = null;
	public $winnerdata = [];

	public $loser = null;
	public $loserdata = [];

	public $enddata = [];

	public function __construct($id, Player $player1, Player $player2, Arena $arena, $prepare_time = 15, $fight_time = 300){
		$this->id = $id;
		$this->players = [
			$player1->getName() => $player1,
			$player2->getName() => $player2
		];
		$this->original_players = $this->players;
		$this->arena = $arena;

		$this->prepare_time = $prepare_time;
		$this->fight_time = $fight_time;

		$this->timer = $prepare_time;

		$arena->teleport($player1, $player2);
		foreach($this->players as $player){

			$player->sendMessage(TextFormat::GRAY . "Welcome to " . TextFormat::YELLOW . "Duels " . TextFormat::RED . "BETA" . TextFormat::GRAY . "! Playing map: " . TextFormat::YELLOW . $arena->getName() . TextFormat::GRAY . ". You have " . TextFormat::YELLOW . $this->prepare_time . TextFormat::GRAY . " seconds to prepare.");
			//$player->showModal(new DuelEnterUi($player, $this));

			KitPvP::getInstance()->getDuels()->setPreferredMap($player);
			foreach(Server::getInstance()->getOnlinePlayers() as $p){
				if(!isset($this->players[$p->getName()])){
					$player->despawnFrom($p);
					$p->despawnFrom($player);
				}
			}
		}
	}

	public function tick(){
		$this->subTimer();

		$timer = $this->getTimer();
		switch($this->getGameStatus()){
			case self::GAME_PREPARE:
				if($timer <= 0){
					$this->setGameStatus(self::GAME_FIGHT);
					$this->setTimer($this->getFightTime());
					foreach($this->getPlayers() as $player){
						$player->sendMessage(TextFormat::RED . "You can now move! Go fight! You have 5 minutes.");
						foreach(Server::getInstance()->getOnlinePlayers() as $p){
							if(!isset($this->players[$p->getName()])){
								$player->despawnFrom($p);
								$p->despawnFrom($player);
							}
						}
					}
					return;
				}
				if(count($this->getPlayers()) < 2){
					$this->end();
					return;
				}
				foreach($this->getPlayers() as $player){
					$player->sendPopup(TextFormat::YELLOW . "Duel: " . TextFormat::AQUA . "Prepare!  " . TextFormat::GRAY .gmdate("(i:s)", $timer));
				}
			break;
			case self::GAME_FIGHT:
				if($timer <= 0){
					$this->end();
					return;
				}
				if(count($this->getPlayers()) < 2){
					$this->end();
					return;
				}
				if($this->getWinner() != null && $this->getLoser() != null){
					$this->end();
					return;
				}
				foreach($this->getPlayers() as $player){
					$player->sendPopup(TextFormat::YELLOW . "Duel: " . TextFormat::RED . "Fight!  " . TextFormat::GRAY . gmdate("(i:s)", $timer));
				}
			break;
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getPlayers(){
		return $this->players;
	}

	public function getArena(){
		return $this->arena;
	}

	public function getPrepareTime(){
		return $this->prepare_time;
	}

	public function getFightTime(){
		return $this->fight_time;
	}

	public function getTimer(){
		return $this->timer;
	}

	public function setTimer($time){
		$this->timer = $time;
	}

	public function subTimer(){
		$this->timer--;
	}

	public function getGameStatus(){
		return $this->status;
	}

	public function setGameStatus($status){
		$this->status = $status;
	}

	public function getWinner(){
		return $this->winner;
	}

	public function setWinner(Player $player){
		$this->winner = $player;
	}

	public function getLoser(){
		return $this->loser;
	}

	public function setLoser(Player $player){
		$this->loser = $player;
	}

	public function leave(Player $player){
		unset($this->getPlayers()[$player->getName()]);
	}

	public function end(){
		$winner = $this->getWinner();
		$loser = $this->getLoser();
		if($this->getGameStatus() == 0 || $this->getPlayers() < 2){
			foreach($this->getPlayers() as $player){
				$player->sendMessage(TextFormat::RED . "Duel ended. (Player left)");
			}
		}elseif($winner == null || $loser == null){
			foreach($this->getPlayers() as $player){
				$player->sendMessage(TextFormat::RED . "Duel ended in a draw!");
			}
		}else{
			$loser->sendMessage(TextFormat::RED . "Lost duel against " . $winner->getName() . "! Better luck next time.");

			$duels = KitPvP::getInstance()->getDuels();
			if(!$duels->hasWon($winner, $loser)){
				$winner->addTechits(50);
				$winner->sendMessage(TextFormat::GREEN . "Won duel against " . $loser->getName() . " and earned 50 techits!");
			}else{
				$winner->sendMessage(TextFormat::GREEN . "Won duel against " . $loser->getName() . "! (No prize given due to already winning against this player.)");
			}

			//Duel stats
			//Duel achievements

			$duels->setWon($winner, $loser);

			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$player->sendMessage(TextFormat::GREEN . $winner->getName() . " won duel on map " . $this->getArena()->getName() . "!");
			}
		}

		$this->close();
	}

	public function close(){
		foreach($this->getPlayers() as $player){
			if($player->getLevel() != null){
				KitPvP::getInstance()->getArena()->exitArena($player);
				KitPvP::getInstance()->getCombat()->getSlay()->resetPlayer($player);
				foreach(Server::getInstance()->getOnlinePlayers() as $p){
					$player->spawnTo($p);
					$p->spawnTo($player);
				}
			}
		}
		KitPvP::getInstance()->getDuels()->removeDuel($this->getId());
	}

}