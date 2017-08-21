<?php namespace kitpvp\combat\bodies\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class DespawnBodyTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$bodies = KitPvP::getInstance()->getCombat()->getBodies();
		foreach($bodies->bodies as $eid => $data){
			if($bodies->canDestroyBody($eid)){
				$bodies->destroyBody($eid);
			}
		}
	}

}