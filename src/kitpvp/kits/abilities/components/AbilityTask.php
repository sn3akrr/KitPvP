<?php namespace kitpvp\kits\abilities\components;

use pocketmine\scheduler\PluginTask;
use pocketmine\level\{
	sound\GhastShootSound,

	particle\FlameParticle
};
use pocketmine\entity\Effect;
use pocketmine\item\Bow;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

class AbilityTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->runs = 0;
	}

	public function onRun(int $currentTick){
		$this->runs++;
		$kits = $this->plugin->getKits();
		foreach($kits->getAbilityTickers() as $ticker){
			$ticker->tick();
		}
		foreach($kits->sessions as $name => $session){
			$player = $session->getPlayer();
			if($player instanceof Player && $session->hasKit()){
				$kit = $session->getKit();
				foreach($kit->getEffects() as $effect){
					if(!$player->hasEffect($effect->getId())){
						$player->addEffect($effect);
					}
				}
			}
		}
		if($this->runs %20 == 0){
			foreach($kits->sessions as $name => $session){
				$player = $session->getPlayer();
				if($player instanceof Player){
					if(!$session->hasKit()){
						if($kits->isInvisible($player)) $kits->setInvisible($player, false);
					}
				}
			}
		}
	}

}