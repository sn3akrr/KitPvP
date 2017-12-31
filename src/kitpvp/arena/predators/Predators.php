<?php namespace kitpvp\arena\predators;

use pocketmine\entity\Entity;
use pocketmine\level\Position;
use kitpvp\arena\predators\entities\{
	Predator
};

use kitpvp\KitPvP;

class Predators{

	public $plugin;

	public $spawners = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		Entity::registerEntity(Predator::class);

		$this->setup();
	}

	public function tick(){
		foreach($this->getSpawners() as $spawner){
			$spawner->tick();
		}
	}

	public function setup(){
		$level = $this->plugin->getServer()->getLevelByName(Structure::LEVEL);
		foreach(Structure::LOCATIONS as $id => $data){
			$this->spawners[] = new Spawner($id, new Position($data["x"], $data["y"], $data["z"], $level));
		}
	}

	public function getSpawners(){
		return $this->spawners;
	}

}