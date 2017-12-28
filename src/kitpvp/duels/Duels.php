<?php namespace kitpvp\duels;

use pocketmine\math\Vector3;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\duels\pieces\{
	Arena,
	Duel,

	Queue,
	MatchedQueue
};
use kitpvp\duels\commands\DuelsCommand;

class Duels{

	public $plugin;

	public $arenas = [];
	public $queues = [];

	public $duels = [];
	public static $duelCount = 1;

	public $requests = [];
	public static $requestCount = 1;

	public $sessions = [];

	public $wins = []; //Saved here, doesn't reset on session termination

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$db = $plugin->database;

		foreach([
			"CREATE TABLE IF NOT EXISTS duels_stats(xuid BIGINT(16) NOT NULL UNIQUE, wins INT NOT NULL DEFAULT '0', losses INT NOT NULL DEFAULT '0')",
		] as $query) $db->query($query);

		$this->setupArenas();
		$this->setupQueues();

		$plugin->getServer()->getCommandMap()->register("duels", new DuelsCommand($plugin, "duels", "Enter duel menu"));
	}

	public function tick(){
		foreach($this->getQueues() as $name => $queue){
			$queue->tick();
		}
		foreach($this->getDuels() as $name => $duel){
			$duel->tick();
		}
		foreach($this->getRequests() as $name => $request){
			$request->tick();
		}
	}

	public function close(){
		foreach($this->sessions as $name => $session){
			$session->save();
		}
	}

	public function onQuit(Player $player){
		if($this->inDuel($player)){
			$duel = $this->getPlayerDuel($player);
			$duel->leave($player);
			$duel->end();
		}
		foreach($this->getQueues() as $queue){
			if($queue->inQueue($player)){
				$queue->removePlayer($player);
			}
		}
		$this->deleteSession($player);
	}

	public function setupArenas(){
		foreach(Structure::ARENAS as $id => $data){
			$p1 = explode(",", $data["spawn1"]);
			$p2 = explode(",", $data["spawn2"]);
			$this->arenas[$id] = new Arena(
				$id,
				$data["name"],
				new Vector3((float) $p1[0], (float) $p1[1], (float) $p1[2]),
				new Vector3((float) $p2[0], (float) $p2[1], (float) $p2[2]),
				$this->plugin->getServer()->getLevelByName($data["level"])
			);
		}
	}

	public function setupQueues(){
		foreach(Structure::QUEUES as $id => $data){
			$this->queues[$id] = new Queue(
				$id,
				$data["name"]
			);
		}
	}

	public function getArenas(){
		return $this->arenas;
	}

	public function getNumberedArenas(){
		$key = 0;
		$arenas = [];
		foreach($this->getArenas() as $arena){
			$arenas[$key] = $arena;
			$key++;
		}
		return $arenas;
	}

	public function getArena($id){
		return $this->getArenas()[$id] ?? null;
	}

	public function getRandomArena(){
		$arenas = $this->getNumberedArenas();
		return $arenas[mt_rand(0, count($arenas) - 1)];
	}

	public function getQueues(){
		return $this->queues;
	}

	public function getQueue($id){
		return $this->getQueues()[$id] ?? null;
	}

	public function getDuels(){
		return $this->duels;
	}

	public function inDuel(Player $player){
		foreach($this->getDuels() as $id => $duel){
			if(isset($duel->getPlayers()[$player->getName()])) return true;
		}
		return false;
	}

	public function getPlayerDuel(Player $player){
		foreach($this->getDuels() as $id => $duel){
			if(isset($duel->getPlayers()[$player->getName()])) return $duel;
		}
		return null;
	}

	public function createDuel(Player $player1, Player $player2, $arena = null){
		if($arena == null) $arena = $this->getRandomArena();
		if(is_string($arena)) $arena = $this->getArena($arena);

		$id = self::$duelCount++;
		$this->duels[$id] = new Duel($id, $player1, $player2, $arena);
	}

	public function removeDuel($id){
		unset($this->duels[$id]);
	}

	public function getRequests(){
		return $this->requests;
	}

	public function getSession(Player $player){
		return $this->sessions[$player->getName()] ?? $this->createSession($player);
	}

	public function createSession(Player $player){
		return $this->sessions[$player->getName()] = new Session($player);
	}

	public function deleteSession(Player $player){
		$this->getSession($player)->save();
		unset($this->sessions[$player->getName()]);
	}

	// Check if player has won against another //
	public function hasWon(Player $winner, Player $loser){
		if(isset($this->wins[$winner->getName()])){
			return isset($this->wins[$winner->getName()][$loser->getName()]);
		}
		return false;
	}

	public function setWon(Player $winner, Player $loser){
		if(!isset($this->wins[$winner->getName()])) $this->wins[$winner->getName()] = [];
		$this->wins[$winner->getName()][$loser->getName()] = true;
	}

}