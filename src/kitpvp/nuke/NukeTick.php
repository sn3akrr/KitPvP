<?php namespace kitpvp\nuke;

use pocketmine\scheduler\PluginTask;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class NukeTick extends PluginTask{

	const FOUND_TIME = 30;
	const COLLECT_TIME = 60;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->runs = 0;
	}

	public function onRun(int $currentTick){
		$nuke = $this->plugin->getNuke();
		$this->runs++;
		if($this->runs %20 == 0){
			switch($nuke->getMode()){
				case 0: //WAIT
					$left = (int) ($nuke->getTime() + $nuke->getWaitTime()) - time();
					if($left <= 0){
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendMessage(TextFormat::AQUA."Nuke> ".TextFormat::GOLD."A nuke has been reported to be dropped on the arena, the military force is still searching to see if this is true...");
						}
						$nuke->setTime();
						$nuke->setMode(1);
					}
				break;
				case 1: //FOUND
					$left = ($nuke->getTime() + self::FOUND_TIME) - time();
					if($left <= 0){
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendMessage(TextFormat::AQUA."Nuke> ".TextFormat::GOLD."A nuke is going to be dropped on the arena in 1 minute! Collect at least 10 shutdown codes to remain safe!");
						}
						$nuke->setTime();
						$nuke->setMode(2);
					}else{
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendTip(TextFormat::AQUA."Nuke".TextFormat::RESET."\n".TextFormat::RED."Search:".TextFormat::RESET." ".TextFormat::YELLOW.$left);
						}
					}
				break;
				case 2: //COLLECT
					$left = ($nuke->getTime() + self::COLLECT_TIME) - time();
					if($left <= 0){
						$count = 0;
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							if($this->plugin->getArena()->inArena($player)){
								if(!$player->getInventory()->contains(new ShutdownCode(0,10))){
									$this->plugin->getCombat()->getSlay()->processSuicide($player);
									$player->sendMessage(TextFormat::AQUA."Nuke> ".TextFormat::RED."You didn't collect enough shutdown codes, killed by Nuke Impact");
									$count++;
								}
							}
						}
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendMessage(TextFormat::AQUA."Nuke> ".TextFormat::GOLD.$count." players were killed that didn't collect enough shutdown codes!");
						}
						$nuke->setTime();
						$nuke->setWaitTime();
						$nuke->setMode(0);

						$nuke->clearShutdownCodes();
					}else{
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendTip(TextFormat::AQUA."Nuke".TextFormat::RESET."\n".TextFormat::RED."Collect:".TextFormat::RESET." ".TextFormat::YELLOW.$left);
						}

						$nuke->dropShutdownCodes();
					}
				break;
			}
		}
	}

}