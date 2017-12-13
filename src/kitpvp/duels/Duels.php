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

	public $pm = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

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
		$this->setPreferredMap($player, null);
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
			if(isset($duel->getPlayers()[$player->getname()])) return true;
		}
		return false;
	}

	public function getPlayerDuel(Player $player){
		foreach($this->getDuels() as $id => $duel){
			if(isset($duel->getPlayers()[$player->getname()])) return $duel;
		}
		return null;
	}

	public function createDuel(Player $player1, Player $player2, $arena = null){
		if($arena == null) $arena = $this->getRandomArena();

		$id = self::$duelCount++;
		$this->duels[$id] = new Duel($id, $player1, $player2, $arena);
	}

	public function removeDuel($id){
		unset($this->duels[$id]);
	}

	public function setPreferredMap(Player $player, $map = null){
		if($map == null){
			unset($this->pm[$player->getName()]);
		}else{
			$this->pm[$player->getName()] = $map;
		}
	}

	public function hasPreferredMap(Player $player){
		return isset($this->pm[$player->getName()]);
	}

	public function getPreferredMap(Player $player){
		return $this->pm[$player->getName()] ?? null;
	}

}