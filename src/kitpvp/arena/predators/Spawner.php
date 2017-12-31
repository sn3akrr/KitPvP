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

use kitpvp\arena\predators\entities\Predator;

class Spawner{

	const SPAWN_DISTANCE = 30;

	public $id;
	public $position;

	public $nextSpawnAttempt = 5;

	public function __construct($id, Position $pos){
		$this->id = $id;
		$this->position = $pos;
	}

	public function tick(){
		if($this->nextSpawnAttempt > 0){
			$this->nextSpawnAttempt--;
		}

		$canSpawn = false;
		foreach($this->getPosition()->getLevel()->getPlayers() as $player){
			if($player->distance($this->getPosition()->asVector3()) <= self::SPAWN_DISTANCE){
				$canSpawn = true;
				break;
			}
		}
		if($canSpawn && $this->nextSpawnAttempt == 0){
			$this->nextSpawnAttempt = 5;

			$x = $this->getPosition()->getX() + mt_rand(-3,3);
			$y = $this->getPosition()->getY();
			$z = $this->getPosition()->getZ() + mt_rand(-3,3);

			/*$entity = new Predator($this->getPosition()->getLevel(), new CompoundTag(" ", [
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
			]), $this);*/
			$entity->spawnToAll();
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getPosition(){
		return $this->position;
	}

}