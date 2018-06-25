<?php namespace kitpvp\combat\special;

use pocketmine\scheduler\Task;

use kitpvp\KitPvP;

class SpecialTask extends Task{

	public $plugin;

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->runs = 0;
	}

	public function onRun(int $currentTick){
		$this->runs++;
		$special = $this->plugin->getCombat()->getSpecial();
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($this->runs %2 == 0){
				if($special->isBleeding($player)){
					$killer = $special->bleeding[$player->getName()]["attacker"];
					if(mt_rand(0,1) == 0) $player->setHealth($player->getHealth() - 2);
				}
			}

		}
	}
}