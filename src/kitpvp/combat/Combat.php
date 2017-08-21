<?php namespace kitpvp\combat;

use kitpvp\KitPvP;
use kitpvp\combat\{
	bodies\Bodies,
	logging\Logging,
	slay\Slay,
	special\Special,
	streaks\Streaks,
	teams\Teams
};

use core\Core;
use core\AtPlayer as Player;

class Combat{

	public $plugin;

	public $bodies;
	public $logging;
	public $slay;
	public $special;
	public $streaks;
	public $teams;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->bodies = new Bodies($plugin, $this);
		$this->logging = new Logging($plugin, $this);
		$this->slay = new Slay($plugin, $this);
		$this->special = new Special($plugin, $this);
		$this->streaks = new Streaks($plugin, $this);
		$this->teams = new Teams($plugin, $this);

		$plugin->getServer()->getPluginManager()->registerEvents(new EventListener($plugin, $this), $plugin);
	}

	public function close(){
		unset($this->getLogging()->logging);
	}

	public function getBodies(){
		return $this->bodies;
	}

	public function getLogging(){
		return $this->logging;
	}

	public function getSlay(){
		return $this->slay;
	}

	public function getSpecial(){
		return $this->special;
	}

	public function getStreaks(){
		return $this->streaks;
	}

	public function getTeams(){
		return $this->teams;
	}

	public function onJoin(Player $player){
		$this->getStreaks()->onJoin($player);
		$this->getSlay()->resetPlayer($player);
	}

	public function onQuit(Player $player){
		if($this->getLogging()->inCombat($player)) $this->getLogging()->punish($player);
		$this->getStreaks()->onQuit($player);
		$this->getSlay()->killChildren($player);
		$this->getSlay()->unsetAssistingPlayers($player);
	}

}
