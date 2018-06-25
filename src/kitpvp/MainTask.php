<?php namespace kitpvp;

use pocketmine\scheduler\Task;

class MainTask extends Task{

	public $plugin;
	public $runs = 0;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$this->runs++;

		if($this->runs % 20 == 0){
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

		$this->plugin->getArena()->getSpectate()->tick();
	}

}