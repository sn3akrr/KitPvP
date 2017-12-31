<?php namespace kitpvp\arena\envoys;

use pocketmine\utils\TextFormat;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\item\Item;

use kitpvp\KitPvP;
use kitpvp\arena\envoys\entities\Envoy;

class Envoys{

	public $plugin;
	public $database;

	public $dropPoints = [];
	public $items = [];

	public $nextDrop = 0;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([
			"CREATE TABLE IF NOT EXISTS envoy_data(xuid BIGINT(16) NOT NULL UNIQUE, collected INT NOT NULL DEFAULT '0')",
		] as $query) $this->database->query($query);

		Entity::registerEntity(Envoy::class);

		$this->setup();
	}

	public function tick(){
		$this->nextDrop--;
		$drop = $this->getNextDrop();
		if($drop <= 0){
			$this->updateNextDrop();
			$point = $this->getRandomDropPoint();
			$point->dropEnvoy();
		}else{
			if($drop % 60 == 0){
				foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
					$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "An envoy will drop in " . TextFormat::RED . gmdate("i:s", $drop));
				}
			}
			if($drop <= 5){
				foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
					if(!$this->plugin->getDuels()->inDuel($player)){
						$player->addTitle(" ", TextFormat::RED . gmdate("i:s", $drop));
					}
				}
			}
		}
	}

	public function setup(){
		$this->level = $level = $this->plugin->getServer()->getLevelByName(Structure::LEVEL);
		foreach(Structure::DROP_POINTS as $id => $drop){
			$this->dropPoints[] = new DropPoint($id, $drop["name"], new Position($drop["x"], $drop["y"], $drop["z"], $level));
		}

		$this->updateNextDrop();

		$this->items = [
			"food" => [
				Item::get(Item::APPLE,0,4),
				Item::get(Item::COOKED_CHICKEN,0,2),
				Item::get(Item::STEAK,0,2),
				Item::get(Item::STEAK,0,4),
			],
			"special" => [

			],
			"powers" => [

			],
		];
	}

	public function getRandomItem($type){
		$type = $this->items[$type];
		return $type[mt_rand(0,count($type) - 1)];
	}

	public function getDropPoints(){
		return $this->dropPoints;
	}

	public function getRandomDropPoint(){
		return $this->getDropPoints()[mt_rand(0, count($this->getDropPoints()) - 1)];
	}

	public function getDropPoint($id){
		foreach($this->getDropPoints() as $drop){
			if($drop->getId() == $id) return $drop;
		}
		return null;
	}

	public function getNextDrop(){
		return $this->nextDrop;
	}

	public function updateNextDrop(){
		$this->nextDrop = mt_rand(120,300);
	}

	public function getRandomItems(){
		$drops = [];
		$rand = mt_rand(0,2);
		for($i = 0; $i <= 4; $i++){
			if(mt_rand(0,1) == 1){
				$drops[] = $this->getRandomItem("food");
			}
		}
		/*for($i = 0; $i <= 2; $i++){
			if(mt_rand(0,5) == 1){
				$drops[] = $this->getRandomItem("special");
			}
		}*/
		return $drops;
	}

}