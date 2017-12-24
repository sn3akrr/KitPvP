<?php namespace kitpvp\combat\special;

use pocketmine\{
	Player,
	Server
};
use pocketmine\utils\TextFormat;

use kitpvp\combat\special\items\types\SpecialWeapon;
use kitpvp\combat\special\event\{
	SpecialDelayEndEvent,
	SpecialEffectStartEvent,
	SpecialEffectEndEvent
};

class SpecialTicker{

	public $name;
	public $formattedname;

	public $trigger;

	public $cooldownrate;
	public $cooldowns = [];

	public $effecttimer;
	public $effects = [];

	public function __construct($name, $formattedname, SpecialWeapon $trigger, $cooldownrate = -1, $effecttimer = -1){
		$this->name = $name;
		$this->formattedname = $formattedname;

		$this->trigger = $trigger;

		$this->cooldownrate = $cooldownrate;
		$this->effecttimer = $effecttimer;
	}

	public function tick(){
		if(!$this->hasCooldownRate()) return;

		foreach($this->cooldowns as $name => $cooldown){
			$player = Server::getInstance()->getPlayerExact($name);
			if($player instanceof Player){
				$this->cooldowns[$name]--;
				if($this->cooldowns[$name] <= 0){
					unset($this->cooldowns[$name]);
					$player->sendTip(TextFormat::GREEN . $this->getFormattedName() . " recharged!");
					Server::getInstance()->getPluginManager()->callEvent($ev = new SpecialDelayEndEvent($player, $this->getName()));
				}else{
					$player->sendTip(TextFormat::RED . "Can use " . $this->getFormattedName() . " in " . $this->cooldowns[$name] . " seconds...");
				}
			}else{
				unset($this->cooldowns[$name]);
			}
		}

		foreach($this->effects as $name => $timer){
			$player = Server::getInstance()->getPlayerExact($name);
			if($player instanceof Player){
				$this->effects[$name]--;
				if($this->effects[$name] <= 0){
					unset($this->effects[$name]);
					Server::getInstance()->getPluginManager()->callEvent($ev = new SpecialEffectEndEvent($player, $this->getName()));
				}
			}else{
				unset($this->cooldowns[$name]);
			}
		}
	}

	public function getName(){
		return $this->name;
	}

	public function getFormattedName(){
		return $this->formattedname;
	}

	public function getTrigger(){
		return $this->trigger;
	}

	public function isTrigger(SpecialWeapon $item){
		$trigger = $this->getTrigger();
		return $item instanceof $trigger;
	}

	public function getCooldownRate(){
		return $this->cooldownrate;
	}

	public function hasCooldownRate(){
		return $this->getCooldownRate() != -1;
	}

	public function getEffectTimer(){
		return $this->effecttimer;
	}

	public function hasEffectTimer(){
		return $this->effecttimer != -1;
	}



	public function hasCooldown(Player $player){
		return isset($this->cooldowns[$player->getName()]);
	}

	public function getCooldown(Player $player){
		return $this->cooldowns[$player->getName()] ?? -1;
	}

	public function use(Player $player, $dynamiccooldown = -1){
		$this->cooldowns[$player->getName()] = ($dynamiccooldown == -1 ? $this->getCooldownRate() : $dynamiccooldown);
	}

	public function startEffect(Player $player, $dynamictimer = -1){
		$this->effects[$player->getName()] = ($dynamictimer == -1 ? $this->getEffectTimer() : $dynamictimer);
		Server::getInstance()->getPluginManager()->callEvent($ev = new SpecialEffectStartEvent($player, $this->getName()));
	}

}