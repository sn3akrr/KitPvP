<?php namespace kitpvp\combat\logging;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;

use core\AtPlayer as Player;

class Logging{

	const COMBAT_TIMEOUT = 15;

	public $plugin;
	public $combat;

	public $logging = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
	}

	public function inCombat(Player $player){
		return isset($this->logging[strtolower($player->getName())]);
	}

	public function setCombat(Player $player, Player $damager){
		$this->logging[strtolower($player->getName())] = [time(),$damager->getName()];
	}

	public function removeCombat(Player $player){
		unset($this->logging[strtolower($player->getName())]);
	}

	public function canRemoveCombat(Player $player){
		return time() - $this->logging[strtolower($player->getName())][0] >= self::COMBAT_TIMEOUT;
	}

	public function getLastHitter(Player $player){
		return $this->plugin->getServer()->getPlayerExact($this->logging[strtolower($player->getName())][1]);
	}

	public function punish(Player $player){
		$dmger = $this->getLastHitter($player);
		$this->combat->getSlay()->processKill($dmger, $player);
		$player->takeTechits(10);
	}

}