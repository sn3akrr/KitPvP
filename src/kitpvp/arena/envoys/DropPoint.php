<?php namespace kitpvp\arena\envoys;

use pocketmine\Server;
use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\{
	CompoundTag,
	ListTag,
	FloatTag,
	DoubleTag,
	ShortTag,
	StringTag
};

use kitpvp\KitPvP;
use kitpvp\arena\envoys\entities\Envoy;

class DropPoint{

	public $id;
	public $name;

	public $position;

	public function __construct($id, $name, Position $position){
		$this->id = $id;
		$this->name = $name;
		$this->position = $position;
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getPosition(){
		return $this->position;
	}

	public function dropEnvoy(){
		$x = $this->getPosition()->getX();
		$y = $this->getPosition()->getY();
		$z = $this->getPosition()->getZ();
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			if(!KitPvP::getInstance()->getDuels()->inDuel($player)){
				$player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "Envoy has spawned at " . TextFormat::LIGHT_PURPLE . $this->getName() . " " . TextFormat::YELLOW . "(".$x.", ".$y.", ".$z.")");
				$player->addTitle(TextFormat::RED . "Envoy", TextFormat::GRAY . "Dropped at ". TextFormat::YELLOW . "(".$x.", ".$y.", ".$z.")", 20, 100, 10);
			}
		}
		$entity = new Envoy($this->getPosition()->getLevel(), new CompoundTag(" ", [
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
			new ShortTag("Health", 30),
			new CompoundTag("Skin", [
				new StringTag("Data", file_get_contents("/home/data/skins/holding_chest.dat")),
				new StringTag("Name", "Standard_Custom")
			]),
		]), $this);
		$entity->spawnToAll();
	}

}