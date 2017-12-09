<?php namespace kitpvp\duels;

use kitpvp\KitPvP;

class Duels{

	public $plugin;

	public $arenas = [];

	public $queues = [];
	public $duels = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->setupArenas();
		$this->setupQueues();
	}

	public function tick(){
		foreach($this->getQueues() as $name => $queue){
			$queue->tick();
		}
		foreach($this->getDuels() as $name => $duel){
			$duel->tick();
		}
	}

	public function setupArenas(){

	}

	public function setupQueues(){

	}

	public function getArenas(){

	}

	public function getQueues(){

	}

	public function getDuels(){

	}

}