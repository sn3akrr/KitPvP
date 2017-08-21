<?php namespace kitpvp\sparring\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class SparringTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$sparring = $this->plugin->getSparring();
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($sparring->isSparring($player)){
				$hits = $sparring->getHits($player);
				$damage = $sparring->getDamage($player);
				$time = $sparring->getTime($player);
				$left = ($time + 60) - time();
				$player->sendTip("Hits: ".$hits." - Damage: ".$damage." - Left: ".$left);
				if($left <= 0){
					$player->sendMessage(TextFormat::AQUA."Sparring> ".TextFormat::RED."Times up! Total hits: ".$hits.", total damage: ".$damage);
					$sparring->stopSpar($player);
				}
			}
		}
	}

}