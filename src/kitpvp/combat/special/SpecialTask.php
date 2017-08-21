<?php namespace kitpvp\combat\special;

use pocketmine\scheduler\PluginTask;

use kitpvp\KitPvP;

class SpecialTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$special = $this->plugin->getCombat()->getSpecial();
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){

			if(isset($special->special[$player->getName()]["decoy"])){
				if(($special->special[$player->getName()]["decoy"] + 5) - time() <= 0){
					if($this->plugin->getKits()->isInvisible($player)) $this->plugin->getKits()->setInvisible($player, false);
					unset($special->special[$player->getName()]["decoy"]);
				}else{
					if(!$this->plugin->getKits()->isInvisible($player)) $this->plugin->getKits()->setInvisible($player, true);
				}

			}

		}
	}
}