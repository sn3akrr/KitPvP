<?php namespace kitpvp\arena\predators;

use pocketmine\entity\Entity;
use pocketmine\level\Position;
use kitpvp\arena\predators\entities\{
	Predator,
	Knight, Pawn, King,
	Robot, Cyborg, PowerMech,
	Jungleman, Caveman, Gorilla,
	Bandit, Cowboy, Sheriff
};

use kitpvp\KitPvP;

class Predators{

	public $plugin;

	public $spawners = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		Entity::registerEntity(Predator::class);

		Entity::registerEntity(Knight::class);
		Entity::registerEntity(Pawn::class);
		Entity::registerEntity(King::class);

		Entity::registerEntity(Robot::class);
		Entity::registerEntity(Cyborg::class);
		Entity::registerEntity(PowerMech::class);

		Entity::registerEntity(Jungleman::class);
		Entity::registerEntity(Caveman::class);
		Entity::registerEntity(Gorilla::class);

		Entity::registerEntity(Bandit::class);
		Entity::registerEntity(Cowboy::class);
		Entity::registerEntity(Sheriff::class);

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
			$this->spawners[] = new Spawner($id, $data["type"], $data["ticks"], $data["distance"] ?? 20, new Position($data["x"], $data["y"], $data["z"], $level));
		}
	}

	public function getSpawners(){
		return $this->spawners;
	}

}