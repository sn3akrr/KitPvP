<?php namespace kitpvp\combat\slay\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class InvincibilityTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$slay = KitPvP::getInstance()->getCombat()->getSlay();
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($slay->isInvincible($player)){
				if($slay->canRemoveInvincibility($player)){
					$slay->removeInvincibility($player);
					$player->sendMessage(TextFormat::AQUA."Invincibility> ".TextFormat::GREEN."You are no longer invincible!");
				}
			}
			if($player->getHealth() == $player->getMaxHealth()){
				$player->setFood(20);
			}else{
				$player->setFood(17);
			}
		}
	}

}