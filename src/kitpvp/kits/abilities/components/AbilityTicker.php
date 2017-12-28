<?php namespace kitpvp\kits\abilities\components;

use pocketmine\{
	Player,
	Server
};

use kitpvp\KitPvP;

class AbilityTicker{

	public $name;

	public $tickRate;
	public $ticks = 0;

	public $ticking = [];

	public function __construct($name, $tickRate){
		$this->name = $name;
		$this->tickRate = $tickRate;
	}

	public function tick(){
		$this->ticks++;
		if($this->ticks % $this->getTickRate() != 0) return;

		foreach($this->ticking as $name){
			$player = Server::getInstance()->getPlayerExact($name);
			if($player instanceof Player){
				$session = KitPvP::getInstance()->getKits()->getSession($player);
				if($session->hasKit()){
					$ability = $session->getKit()->getAbility($this->getName());
					if($ability != null && $ability->isActive()){
						$keep = $ability->tick();
						if(!$keep){
							unset($this->ticking[$name]);
						}
					}else{
						unset($this->ticking[$name]);
					}
				}else{
					unset($this->ticking[$name]);
				}
			}else{
				unset($this->ticking[$name]);
			}
		}
	}

	public function getName(){
		return $this->name;
	}

	public function getTickRate(){
		return $this->tickRate;
	}

	public function getTicking(){
		return $this->ticking;
	}

	public function addTicking(Player $player){
		$this->ticking[] = $player->getName();
	}

}