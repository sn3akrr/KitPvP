<?php namespace kitpvp\combat\streaks;

use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;

use core\Core;
use core\AtPlayer as Player;

class Streaks{

	public $plugin;
	public $combat;

	public $streaks = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
	}

	public function onJoin(Player $player){
		$this->streaks[strtolower($player->getName())] = 0;
	}

	public function onQuit(Player $player){
		unset($this->streaks[strtolower($player->getName())]);
	}

	public function addStreak(Player $player){
		$this->setStreak($player, $this->getStreak($player) + 1);
		if($this->getStreak($player) % 5 == 0){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
				$p->sendMessage(TextFormat::AQUA."Streak> ".TextFormat::GREEN.$player->getName()." is on a ".$this->getStreak($player)." kill streak!");
			}
		}
		$player->setXpLevel($this->getStreak($player));
	}

	public function getStreak(Player $player){
		return $this->streaks[strtolower($player->getName())];
	}

	public function setStreak(Player $player, $value){
		$this->streaks[strtolower($player->getName())] = $value;
	}

	public function resetStreak(Player $player, Player $killer){
		$reward = $this->hasSignificantStreak($player);
		$streak = $this->getStreak($player);
		$this->setStreak($player, 0);
		if($reward){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
				$p->sendMessage(TextFormat::AQUA."Streak> ".TextFormat::LIGHT_PURPLE.$killer->getName()." broke ".$player->getName()."'s kill streak of ".$streak." and earned ".($streak * 2)." Techits!");
			}
			$killer->addTechits($streak * 2);
			$killer->addGlobalExp($streak);
		}
		$player->setXpLevel(0);

		$lb = $this->plugin->getLeaderboard();
		if($lb->getStreak($player) < $streak){
			$lb->setStreak($player, $streak);
			$player->sendMessage(TextFormat::GREEN . "New highest streak reached!");
		}
	}

	public function hasSignificantStreak(Player $player){
		return $this->getStreak($player) >= 5;
	}

}