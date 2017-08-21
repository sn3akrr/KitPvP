<?php namespace kitpvp\combat\logging\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class LoggingTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$logging = KitPvP::getInstance()->getCombat()->getLogging();
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($logging->inCombat($player)){
				if($logging->canRemoveCombat($player)){
					$logging->removeCombat($player);
					$player->sendMessage(TextFormat::AQUA."Logging> ".TextFormat::GREEN."You are no longer in combat mode!");
				}
			}
		}
	}

}