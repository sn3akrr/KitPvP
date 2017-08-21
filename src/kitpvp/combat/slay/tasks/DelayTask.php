<?php namespace kitpvp\combat\slay\tasks;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class DelayTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun(int $currentTick){
		$slay = KitPvP::getInstance()->getCombat()->getSlay();
		if(!empty($slay->delay)){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
				if(isset($slay->delay[$player->getName()])){
					$delay = $slay->delay[$player->getName()];
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
					$slay->delay[$player->getName()]--;
					if($slay->delay[$player->getName()] < 0){
						unset($slay->delay[$player->getName()]);
						$player->addActionBarMessage(" ");
					}
				}
			}
		}
	}

}