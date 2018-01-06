<?php namespace kitpvp\combat\logging;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;

class Logging{

	const COMBAT_TIMEOUT = 15;

	public $plugin;
	public $combat;

	public $logging = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
	}

	public function tick(){
		foreach($this->logging as $name => $data){
			$player = $this->plugin->getServer()->getPlayerExact($name);
			if($player instanceof Player){
				if($this->canRemoveCombat($player)){
					$this->removeCombat($player);
					$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You are no longer in combat mode!");
				}
			}else{
				unset($this->logging[$name]);
			}
		}
	}

	public function inCombat(Player $player){
		return isset($this->logging[$player->getName()]);
	}

	public function setCombat(Player $player, Player $damager){
		$this->logging[$player->getName()] = [time(),$damager->getName()];
	}

	public function removeCombat(Player $player){
		unset($this->logging[$player->getName()]);
	}

	public function canRemoveCombat(Player $player){
		return time() - $this->logging[$player->getName()][0] >= self::COMBAT_TIMEOUT;
	}

	public function getLastHitter(Player $player){
		return $this->plugin->getServer()->getPlayerExact($this->logging[$player->getName()][1]);
	}

	public function punish(Player $player){
		$dmger = $this->getLastHitter($player);
		$this->combat->getSlay()->processKill($dmger, $player);
		$player->takeTechits(10);
	}

}