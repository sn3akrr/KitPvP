<?php namespace kitpvp\arena;

use pocketmine\level\Position;
use pocketmine\level\Location;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;

use kitpvp\KitPvP;
use kitpvp\arena\{
	envoys\Envoys,
	predators\Predators
};

use core\vote\Vote;
use core\Core;

class Arena{

	public $plugin;

	public $envoys;

	public $regions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->envoys = new Envoys($plugin);
		$this->predators = new Predators($plugin);

		$this->setupRegions();
	}

	public function tick(){
		$this->getEnvoys()->tick();
		$this->getPredators()->tick();
	}

	public function getEnvoys(){
		return $this->envoys;
	}

	public function getPredators(){
		return $this->predators;
	}

	public function onJoin(Player $player){
		$this->getEnvoys()->createSession($player);
		//$this->getPredators()->createSession($player);
	}

	public function onQuit(Player $player){
		$this->getEnvoys()->deleteSession($player);
		//$this->getPredators()->deleteSession($player);
	}

	public function setupRegions(){
		$level = $this->plugin->getServer()->getLevelByName("atm");
		foreach([
			new Region("the wild west", [
				//Gold Mines
				new Location(3114.5, 47, -92.5, 90, 0, $level),
				new Location(3098.5, 47, -92.5, 0, 0, $level),
				new Location(3054.5, 47, -102.5, -135, 0, $level),
				new Location(3073.5, 47, -92.5, 90, -90, $level),

				//Train Station
				new Location(2988.5, 50, -72.5, -45, 0, $level),
				new Location(2989.5, 50, -64.5, -45, 0, $level),

				//Sheriff Building
				new Location(3014.5, 49, -92.5, -45, 0, $level),
				new Location(3016.5, 49, -84.5, 180, 0, $level),

				//Outside Thingies
				new Location(3064.5, 47, -27.5, 180, 0, $level),
				new Location(3043.5, 47, -26.5, 180, 0, $level),
				new Location(3028.5, 47, -31.5, 180, 0, $level),

				//Bank
				new Location(3016.5, 48, -52.5, -45, 0, $level),
				new Location(3021.5, 48, -40.5, 180, 0, $level),

				//Saloon
				new Location(3018.5, 48, -62.5, 225, 0, $level),
				new Location(3018.5, 48, -74.5, -45, 0, $level),

				//Saloon 2? (Or restaurant)
				new Location(3085.5, 49, -38.5, -135, 0, $level),
				new Location(3089.5, 49, -45.5, 90, 0, $level),
			]),
			new Region("the mountains", [
				//Ground
				new Location(2985.5, 47, 42.5, 180, 0, $level),
				new Location(2980.5, 47, 12.5, 0, 0, $level),
				new Location(3000.5, 47, -3.5, 45, 0, $level),
				new Location(2985.5, 47, 12.5, 0, 0, $level),

				//Mountains
				new Location(3004.5, 54, 22.5, 135, 0, $level),
				new Location(2958.5, 52, 17.5, -135, 0, $level),
				new Location(2963.5, 57, 22.5, -90, 0, $level),
				new Location(2942.5, 74, 48.5, -135, 0, $level),
			]),
			new Region("the city", [
				//Roads
				new Location(3113.5, 47, 21.5, -135, 0, $level),
				new Location(3113.5, 47, 4.5, -45, 0, $level),
				new Location(3151.5, 47, 21.5, 45, 0, $level),
				new Location(3151.5, 47, 48.5, -45, 0, $level),
				new Location(3113.5, 47, 48.5, 225, 0, $level),

				//Building interiors
				new Location(3142.5, 48, 73.5, 135, 0, $level),
				new Location(3097.5, 49, 42.5, 270, 0, $level),
				new Location(3102.5, 75, 43.5, 135, 0, $level),
				new Location(3092.5, 53, 6.5, 0, 0, $level),
				new Location(3086.5, 48, 5.5, -45, 0, $level),
				new Location(3133.5, 53, 20.5, 45, 0, $level),
				new Location(3160.5, 56, -43.5, 45, 0, $level),
			]),
			new Region("the castle", [
				//Interior First Floor
				new Location(3070.5, 53, 134.5, 45, 0, $level),
				new Location(3073.5, 53, 139.5, 135, 0, $level),
				new Location(3062, 53, 141.5, 180, 0, $level),
				new Location(3046, 53, 142, 180, 0, $level),
				new Location(3038.5, 53, 140.5, 270, 0, $level),

				//Interior Second Floor
				new Location(3071.5, 69, 139.5, 135, 0, $level),
				new Location(3062.5, 69, 142.5, 135, 0, $level),
				new Location(3054.5, 71, 128.5, 0, 0, $level),
				new Location(3047.5, 69, 142.5, 225, 0, $level),
				new Location(3054.5, 69, 138.5, 180, 0, $level),
				new Location(3065.5, 71, 132.5, 45, 0, $level),

				//Left Wall
				new Location(3078.5, 68, 86.5, 0, 0, $level),
				new Location(3093.5, 68, 119.5, 90, 0, $level),
				new Location(3093.5, 68, 111.5, 90, 0, $level),

				//Right Wall
				new Location(3028.5, 68, 86.5, 0, 0, $level),
				new Location(3016.5, 68, 111.5, 270, 0, $level),
				new Location(3016.5, 68, 117.5, 270, 0, $level),


				//Ground
				new Location(3078.5, 47, 127.5, 135, 0, $level),
				new Location(3067.5, 47, 124.5, 135, 0, $level),
				new Location(3070.5, 47, 107.5, 90, 0, $level),
				new Location(3075.5, 47, 92.5, 45, 0, $level),
				new Location(3060.5, 47, 85.5, 45, 0, $level),

				new Location(3040.5, 47, 126.5, 225, 0, $level),
				new Location(3045.5, 47, 113.5, 225, 0, $level),
				new Location(3037.5, 47, 106.5, 270, 0, $level),
				new Location(3045.5, 47, 86.5, 315, 0, $level),
				new Location(3047.5, 47, 95.5, 225, 0, $level),
			]),
		] as $region) $this->regions[$region->getName()] = $region;
	}

	public function getRegions(){
		return $this->regions;
	}

	public function getNumberedRegions(){
		$r = [];
		$key = 0;
		foreach($this->getRegions() as $region){
			$r[$key] = $region;
			$key++;
		}
		return $r;
	}

	public function getRandomRegion(){
		return $this->getNumberedRegions()[mt_rand(0,count($this->getNumberedRegions()) - 1)];
	}

	public function getPositionClosestTo(Player $player){
		$distance = 99999;
		$r = null;
		$pos = null;
		foreach($this->regions as $region){
			foreach($region->getPositions() as $position){
				if($position->distance($player) < $distance){
					$distance = $position->distance($player);
					$r = $region;
					$pos = $position;
				}
			}
		}
		return [$r,$pos];
	}

	public function inArena(Player $player){
		return $player->getLevel()->getName() == "atm";
	}

	public function inSpawn(Player $player){
		return $player->getLevel()->getName() == "m4";
	}

	public function tpToArena(Player $player){
		$combat = $this->plugin->getCombat();
		$teams = $combat->getTeams();
		if($teams->inTeam($player)){
			$team = $teams->getPlayerTeam($player);
			$member = $team->getOppositeMember($player);
			if($this->inArena($member)){
				$region = $this->getPositionClosestTo($member);
				$player->teleport(array_pop($region));
			}else{
				$region = $this->getRandomRegion();
				$player->teleport($region->getRandomPosition());
			}
		}else{
			$region = $this->getRandomRegion();
			$player->teleport($region->getRandomPosition());
		}
		if(is_array($region)) $region = $region[0];
		$name = ucwords($region->getName());
		$player->addTitle(TextFormat::AQUA . "Prepare...", TextFormat::GOLD . $name, 10, 40, 10);

		$combat->getBodies()->addAllBodies($player);
		$combat->getSlay()->setInvincible($player);

		$kits = $this->plugin->getKits();
		$session = $kits->getSession($player);
		if(!$session->hasKit()){
			$kits->getKit("noob")->equip($player);
			$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You were automatically given the " . TextFormat::YELLOW . "Noob" . TextFormat::GRAY . " kit!");
		}

		$duels = $this->plugin->getDuels();
		$queues = $duels->getQueues();
		foreach($queues as $queue){
			if($queue->inQueue($player)){
				$queue->removePlayer($player);
				$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Left " . TextFormat::AQUA . $queue->getName() . TextFormat::GRAY . " queue.");
			}
		}

		if(isset($this->plugin->jump[$player->getName()])){
			$attribute = $player->getAttributeMap()->getAttribute(5);
			$attribute->setValue($attribute->getValue() / (1 + 0.2 * 5), true);
		}

		$pk = new GameRulesChangedPacket();
		$pk->gameRules["showcoordinates"] = [1, true];
		$player->dataPacket($pk);
	}

	public function exitArena(Player $player){
		$player->teleport(...$this->getSpawnPosition());

		$this->plugin->getCombat()->getBodies()->removeAllBodies($player);
		$this->plugin->getKits()->getSession($player)->removeKit();

		unset($this->plugin->jump[$player->getName()]);

		$pk = new GameRulesChangedPacket();
		$pk->gameRules["showcoordinates"] = [1, false];
		$player->dataPacket($pk);
	}

	public function getSpawnPosition(){
		return [new Position(81.5,69,201.5, $this->plugin->getServer()->getLevelByName("m4")), 180];
	}

}