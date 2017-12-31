<?php namespace kitpvp;

use pocketmine\scheduler\PluginTask;

class MainTask extends PluginTask{

	public $plugin;
	public $runs = 0;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$this->runs++;

		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->getHealth() == $player->getMaxHealth()){
				$player->setFood(20);
			}else{
				$player->setFood(17);
			}
		}

		$this->plugin->getArena()->tick();

		$this->plugin->getCombat()->tick();

		$this->plugin->getDuels()->tick();
	}

}