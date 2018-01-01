<?php namespace kitpvp\arena\predators;

use pocketmine\level\Position;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\{
	CompoundTag,
	ListTag,
	FloatTag,
	DoubleTag,
	ShortTag,
	StringTag
};
use pocketmine\Server;
use kitpvp\arena\predators\entities\{
	Predator,
	Knight, Pawn, King,
	Robot, Cyborg, PowerMech,
	Jungleman, Caveman, Gorilla,
	Bandit, Cowboy, Sheriff
};

class Spawner{

	public $id;
	public $type;

	public $baseSpawnRate;
	public $spawnDistance;

	public $neededOnline;

	public $position;

	public $spawnRate = 5;

	public function __construct($id, $type, $spawnRate, $spawnDistance = -1, $neededOnline = -1, Position $pos){
		$this->id = $id;
		$this->type = $type;
		$this->baseSpawnRate = $spawnRate;
		$this->spawnDistance = $spawnDistance;
		$this->neededOnline = $neededOnline;

		$this->position = $pos;
	}

	public function tick(){
		if($this->spawnRate > 0){
			$this->spawnRate--;
		}

		$canSpawn = false;
		foreach($this->getPosition()->getLevel()->getPlayers() as $player){
			if($player->distance($this->getPosition()->asVector3()) <= $this->getSpawnDistance()){
				$canSpawn = true;
				break;
			}
		}

		if($canSpawn && $this->spawnRate == 0 && count(Server::getInstance()->getOnlinePlayers()) >= $this->getNeededOnline()){
			$this->spawnRate = $this->baseSpawnRate;
			$predators = 0;
			$type = 0;
			foreach($this->getPosition()->getLevel()->getEntities() as $e){
				if($e instanceof Predator){
					$predators++;
					if(strtolower($e->getType()) == $this->getType()) $type++;
				}
				if(
					$predators >= $this->getBaseSpawnLimit() || 
					$type >= $this->getTypeSpawnLimit()
				){
					return;
				}
			}
			$entity = $this->getEntity();
			$entity->spawnToAll();
		}
		if($this->spawnRate == 0) $this->spawnRate = $this->baseSpawnRate;

	}

	public function getEntity(){
		$x = $this->getPosition()->getX() + mt_rand(-3,3);
		$y = $this->getPosition()->getY();
		$z = $this->getPosition()->getZ() + mt_rand(-3,3);

		$level = $this->getPosition()->getLevel();
		$nbt = new CompoundTag(" ", [
			new ListTag("Pos", [
				new DoubleTag("", $x),
				new DoubleTag("", $y),
				new DoubleTag("", $z)
			]),
			new ListTag("Motion", [
				new DoubleTag("", 0),
				new DoubleTag("", 0),
				new DoubleTag("", 0)
			]),
			new ListTag("Rotation", [
				new FloatTag("", 0),
				new FloatTag("", 0),
			]),
			new ShortTag("Health", 20),
			new CompoundTag("Skin", [
				new StringTag("Data", file_get_contents("/home/data/skins/holding_chest.dat")),
				new StringTag("Name", "Standard_Custom")
			]),
		]);

		switch($this->getType()){
			case "knight":
				$entity = new Knight($level, $nbt);
			break;
			case "pawn":
				$entity = new Pawn($level, $nbt);
			break;
			case "king":
				$entity = new King($level, $nbt);
			break;

			case "robot":
				$entity = new Robot($level, $nbt);
			break;
			case "cyborg":
				$entity = new Cyborg($level, $nbt);
			break;
			case "powermech":
				$entity = new PowerMech($level, $nbt);
			break;

			case "jungleman":
				$entity = new Jungleman($level, $nbt);
			break;
			case "caveman":
				$entity = new Caveman($level, $nbt);
			break;
			case "gorilla":
				$entity = new Gorilla($level, $nbt);
			break;

			case "bandit":
				$entity = new Bandit($level, $nbt);
			break;
			case "cowboy":
				$entity = new Cowboy($level, $nbt);
			break;
			case "sheriff":
				$entity = new Sheriff($level, $nbt);
			break;
		}

		return $entity;
	}

	public function getId(){
		return $this->id;
	}

	public function getType(){
		return $this->type;
	}

	public function getBaseSpawnLimit(){
		return Structure::SPAWN_LIMITS["base"];
	}

	public function getTypeSpawnLimit(){
		return Structure::SPAWN_LIMITS[$this->getType()];
	}

	public function getSpawnDistance(){
		return $this->spawnDistance == -1 ? 9999 : $this->spawnDistance;
	}

	public function getNeededOnline(){
		return $this->neededOnline;
	}

	public function getPosition(){
		return $this->position;
	}

}