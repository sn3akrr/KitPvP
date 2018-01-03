<?php namespace kitpvp\combat\streaks;

use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\entity\Entity;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;
use kitpvp\arena\predators\entities\Predator;

use core\Core;

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

		$as = KitPvP::getInstance()->getAchievements()->getSession($player);
		switch($value){
			case 5:
				if(!$as->hasAchievement("streak_1")) $as->get("streak_1");
			break;
			case 10:
				if(!$as->hasAchievement("streak_2")) $as->get("streak_2");
			break;
			case 20:
				if(!$as->hasAchievement("streak_3")) $as->get("streak_3");
			break;
			case 25:
				if(!$as->hasAchievement("streak_4")) $as->get("streak_4");
			break;
		}
	}

	public function resetStreak(Player $player, Entity $killer){
		$reward = $this->hasSignificantStreak($player);
		$streak = $this->getStreak($player);
		$this->setStreak($player, 0);
		$player->setXpLevel(0);

		$lb = $this->plugin->getLeaderboard();
		if($lb->getStreak($player) < $streak){
			$lb->setStreak($player, $streak);
			$player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "New highest streak of " . TextFormat::GREEN . $streak . TextFormat::GRAY . " has been recorded! Good job!");
		}

		if($killer instanceof Player){
			if($reward){
				foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
					$p->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::YELLOW . $killer->getName() . TextFormat::GRAY . " broke " . TextFormat::YELLOW . $player->getName() . "'s " . TextFormat::GRAY . "kill streak of " . TextFormat::WHITE . $streak . TextFormat::GRAY . " and earned ". TextFormat::AQUA . ($streak * 2) . " Techits" . TextFormat::GRAY . "!");
				}
				$killer->addTechits($streak * 2);
			}
			$a = KitPvP::getInstance()->getAchievements();
			if($streak >= 5){
				$as = $a->getSession($player);
				if(!$as->hasAchievement("streak_killer")) $as->get("streak_killer");
				if($streak == 23){
					$as = $a->getSession($player);
					if($as->hasAchievement("malone_streak")) $as->get("malone_streak");
				}
			}
		}elseif($killer instanceof Predator){
			if($reward){
				foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
					$p->sendMessage(TextFormat::LIGHT_PURPLE . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::YELLOW . $player->getName() . TextFormat::GRAY . " lost their streak to a " . TextFormat::DARK_PURPLE . $killer->getType() . TextFormat::GRAY . "! How embarrassing!");
				}
			}
		}
	}

	public function hasSignificantStreak(Player $player){
		return $this->getStreak($player) >= 5;
	}

}