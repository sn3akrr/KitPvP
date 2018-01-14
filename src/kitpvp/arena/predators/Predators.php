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

	public $sessions = [];

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

	public function close(){
		foreach($this->sessions as $name => $session){
			$session->save();
		}
	}

	public function tick(){
		foreach($this->getSpawners() as $spawner){
			$spawner->tick();
		}
	}

	public function setup(){
		$level = $this->plugin->getServer()->getLevelByName(Structure::LEVEL);
		foreach(Structure::LOCATIONS as $id => $data){
			$this->spawners[] = new Spawner($id, $data["type"], $data["ticks"], $data["distance"] ?? 20, $data["online"] ?? -1, new Position($data["x"], $data["y"], $data["z"], $level));
		}
	}

	public function getSpawners(){
		return $this->spawners;
	}

	public function getPredatorTypes(){
		return [
			"knight", "pawn", "king",
			"robot", "cyborg", "powermech",
			"jungleman", "caveman", "gorilla",
			"bandit", "cowboy", "sheriff",
		];
	}

	public function getClassFromType($type){
		switch(strtolower($type)){
			case "knight":
				return Knight::class;
			case "pawn":
				return Pawn::class;
			case "king":
				return King::class;

			case "robot":
				return Robot::class;
			case "cyborg":
				return Cyborg::class;
			case "powermech":
				return PowerMech::class;

			case "jungleman":
				return Jungleman::class;
			case "caveman":
				return Caveman::class;
			case "gorilla":
				return Gorilla::class;

			case "bandit":
				return Bandit::class;
			case "cowboy":
				return Cowboy::class;
			case "sheriff":
				return Sheriff::class;
		}
		return "";
	}

	public function createSession($player){
		$session = new Session($player);
		$this->sessions[$session->getUser()->getGamertag()] = $session;

		return $session;
	}

	public function getSession($player){
		if($player instanceof Player) $player = $player->getName();
		
		return $this->sessions[$player] ?? $this->createSession($player);
	}

	public function deleteSession($player, $save = true){
		if($player instanceof Player) $player = $player->getName();

		if($save){
			$this->sessions[$player]->save();
		}

		unset($this->sessions[$player]);
	}

}