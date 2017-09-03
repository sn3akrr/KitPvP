<?php namespace kitpvp\combat;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

class CombatTask extends PluginTask{

	public $plugin;
	public $runs = 0;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$this->runs++;

		$combat = $this->plugin->getCombat();
		$slay = $combat->getSlay();

		if($this->runs %20 == 0){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
				//Invincibility
				if($slay->isInvincible($player)){
					if($slay->canRemoveInvincibility($player)){
						$slay->removeInvincibility($player);
						$player->sendMessage(TextFormat::AQUA."Invincibility> ".TextFormat::GREEN."You are no longer invincible!");
					}
				}

				$logging = $combat->getLogging();
				if($logging->inCombat($player)){
					if($logging->canRemoveCombat($player)){
						$logging->removeCombat($player);
						$player->sendMessage(TextFormat::AQUA."Logging> ".TextFormat::GREEN."You are no longer in combat mode!");
					}
				}

				$bodies = $combat->getBodies();
				foreach($bodies->bodies as $eid => $data){
					if($bodies->canDestroyBody($eid)){
						$bodies->destroyBody($eid);
					}
				}

				//Food handling
				if($player->getHealth() == $player->getMaxHealth()){
					$player->setFood(20);
				}else{
					$player->setFood(17);
				}
			}
		}

		if($this->runs %2 == 0){
			if(!empty($slay->delay)){
				foreach($slay->delay as $name => $delay){
					$player = $this->plugin->getServer()->getPlayerExact($name);
					if($player instanceof Player){
						switch($delay){
							case 5:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::RED."||||||||||||||||||||".TextFormat::GRAY."]");
							break;
							case 4:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::GREEN."||||".TextFormat::RED."||||||||||||||||".TextFormat::GRAY."]");
							break;
							case 3:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::GREEN."||||||||".TextFormat::RED."||||||||||||".TextFormat::GRAY."]");
							break;
							case 2:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::GREEN."||||||||||||".TextFormat::RED."||||||||".TextFormat::GRAY."]");
							break;
							case 1:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::GREEN."||||||||||||||||".TextFormat::RED."||||".TextFormat::GRAY."]");
							break;
							case 0:
								$player->addActionBarMessage(TextFormat::GRAY."[".TextFormat::GREEN."||||||||||||||||||||".TextFormat::GRAY."]");
							break;
						}
						$slay->delay[$name]--;
						if($slay->delay[$name] < 0){
							unset($slay->delay[$name]);
							$player->addActionBarMessage(" ");
						}
					}
				}
			}
		}
	}

}