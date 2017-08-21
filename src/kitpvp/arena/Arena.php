<?php namespace kitpvp\arena;

use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;

use kitpvp\KitPvP;
use core\AtPlayer as Player;
use core\Core;

class Arena{

	public $plugin;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

	public function inArena(Player $player){
		return $player->getLevel()->getName() == "KitArena";
	}

	public function tpToArena(Player $player){
		$level = $this->plugin->getServer()->getLevelByName("KitArena");
		$positions = [
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
		];
		$tpto = $positions[mt_rand(0,count($positions) - 1)];
		//$player->teleport($tpto);
		$player->delayTp($tpto);

		$combat = $this->plugin->getCombat();
		$combat->getBodies()->addAllBodies($player);

		$combat->getSlay()->setInvincible($player);

		$kit = $this->plugin->getKits();
		if(!$kit->hasKit($player)){
			$kit->getKit("noob")->equip($player);
			$player->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."You were automatically given the default kit!");
		}
	}

	public function exitArena(Player $player){
		$tpto = new Position(129.5,22,135.5, $this->plugin->getServer()->getDefaultLevel());

		//$player->teleport($tpto, 180);
		$player->delayTp($tpto);

		$this->plugin->getCombat()->getBodies()->removeAllBodies($player);
		$this->plugin->getKits()->setEquipped($player, false);
	}

}