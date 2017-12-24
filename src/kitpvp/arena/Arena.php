<?php namespace kitpvp\arena;

use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

use core\vote\Vote;
use core\Core;

class Arena{

	public $plugin;
	public $positions;

	public $regions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$level = $this->plugin->getServer()->getLevelByName("KitArena");
		$region = new Region("main", [
			new Position(152,75,89,$level),
			new Position(128,58,113,$level),
			new Position(128,57,124,$level),
			new Position(139,59,114,$level),
			new Position(170,71,108,$level),
			new Position(154,69,127,$level),
			new Position(138,69,139,$level),
			new Position(117,69,143,$level),
			new Position(105,69,123,$level),
			new Position(108,69,106,$level),
			new Position(105,68,90,$level),
			new Position(118,71,86,$level),
			new Position(129,71,84,$level),
			new Position(146,68,88,$level),
			new Position(147,69,99,$level),
			new Position(161,70,95,$level),
			new Position(168,84,101,$level),
			new Position(164,84,93,$level),
			new Position(145,87,82,$level),
			new Position(131,83,82,$level),
			new Position(112,83,84,$level),
			new Position(103,82,88,$level),
			new Position(101,68,94,$level),
			new Position(109,69,92,$level),
			new Position(87,70,122,$level),
			new Position(96,71,154,$level),
		]);
		$this->regions["main"] = $region;
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
		$pos = null;
		foreach($this->regions as $region){
			foreach($region->getPositions() as $position){
				if($position->distance($player) < $distance){
					$distance = $position->distance($player);
					$pos = $position;
				}
			}
		}
		return $pos;
	}

	public function inArena(Player $player){
		return $player->getLevel()->getName() == "KitArena";
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
				$player->teleport($this->getPositionClosestTo($member));
			}else{
				$player->teleport($this->getRandomRegion()->getRandomPosition());
			}
		}else{
			$player->teleport($this->getRandomRegion()->getRandomPosition());
		}

		$combat->getBodies()->addAllBodies($player);
		$combat->getSlay()->setInvincible($player);

		$kits = $this->plugin->getKits();
		$session = $kits->getSession($player);
		if(!$session->hasKit()){
			$kits->getKit("noob")->equip($player);
			$player->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."You were automatically given the Noob kit!");
		}

		$duels = $this->plugin->getDuels();
		$queues = $duels->getQueues();
		foreach($queues as $queue){
			if($queue->inQueue($player)){
				$queue->removePlayer($player);
				$player->sendMessage(TextFormat::RED . "Left '" . $queue->getName() . "' queue.");
			}
		}

		if(isset($this->plugin->jump[$player->getName()])){
			$attribute = $player->getAttributeMap()->getAttribute(5);
			$attribute->setValue($attribute->getValue() / (1 + 0.2 * 5), true);
		}
	}

	public function exitArena(Player $player){
		$player->teleport(...$this->getSpawnPosition());

		$this->plugin->getCombat()->getBodies()->removeAllBodies($player);
		$this->plugin->getKits()->getSession($player)->removeKit();

		unset($this->plugin->jump[$player->getName()]);
	}

	public function getSpawnPosition(){
		return [new Position(81.5,69,201.5, $this->plugin->getServer()->getLevelByName("m4")), 180];
	}

}