<?php namespace kitpvp\duels\pieces;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\level\{
	Level,
	Position
};

class Arena{

	public $id;
	public $name;

	public $spawn1;
	public $spawn2;
	public $level;

	public function __construct($id, $name, Vector3 $spawn1, Vector3 $spawn2, Level $level){
		$this->id = $id;
		$this->name = $name;

		$this->spawn1 = $spawn1;
		$this->spawn2 = $spawn2;
		$this->level = $level;

		echo $this->spawn1->getX() . "," . $this->spawn1->getY() . "," . $this->spawn1->getZ(), PHP_EOL;
		echo $this->spawn2->getX() . "," . $this->spawn2->getY() . "," . $this->spawn2->getZ(), PHP_EOL;

	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getSpawn1(){
		return $this->spawn1;
	}

	public function getSpawn2(){
		return $this->spawn2;
	}

	public function getLevel(){
		return $this->level;
	}

	public function teleport(Player $player1, Player $player2){
		$player1->teleport($this->getLevel()->getSpawnLocation());
		$player2->teleport($this->getLevel()->getSpawnLocation());

		$player1->teleport($this->getSpawn1());
		$player2->teleport($this->getSpawn2());
	}

}