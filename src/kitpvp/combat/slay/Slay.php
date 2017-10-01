<?php namespace kitpvp\combat\slay;

use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\{
	AddEntityPacket,
	RemoveEntityPacket
};
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\{
	Entity,
	Projectile
};

use kitpvp\KitPvP;
use kitpvp\combat\Combat;

use core\AtPlayer as Player;
use core\Core;

class Slay{

	public $plugin;
	public $combat;
	public $lb;

	public $invincible = [];
	public $delay = [];

	public $lastkilledby = [];
	public $assists = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
		$this->lb = $plugin->getLeaderboard();
	}

	public function isDelayed(Player $player){
		return isset($this->delay[$player->getName()]);
	}

	public function setDelay(Player $player){
		$this->delay[$player->getName()] = 5;
	}

	public function addKill(Player $player){
		$this->lb->addKill($player);
		Core::getInstance()->getStats()->getKd()->addKill($player);

		$teams = $this->combat->getTeams();
		if($teams->inTeam($player)){
			$teams->addTeamKill($teams->getPlayerTeamUid($player));
		}
	}

	public function getKills(Player $player){
		return $this->lb->getKills($player, "alltime");
	}

	public function addDeath(Player $player){
		$this->lb->addDeath($player);
		Core::getInstance()->getStats()->getKd()->addDeath($player);

		$teams = $this->combat->getTeams();
		if($teams->inTeam($player)){
			$teams->addTeamDeath($teams->getPlayerTeamUid($player));
		}
	}

	public function getDeaths(Player $player){
		return $this->lb->getDeaths($player, "alltime");
	}

	public function getKdr(Player $player){
		return ($this->getDeaths($player) == 0 ? $this->getKills($player) : round($this->getKills($player) / $this->getDeaths($player), 2));
	}

	public function processKill(Player $killer, Player $dead){
		$this->addKill($killer);
		$this->combat->getStreaks()->addStreak($killer);
		$this->combat->getLogging()->removeCombat($killer);
		$killer->addTechits(5);

		$this->strikeLightning($dead);
		$this->addDeath($dead);
		$this->combat->getLogging()->removeCombat($dead);
		$this->combat->getBodies()->addBody($dead);
		$this->plugin->getArena()->exitArena($dead);
		$this->resetPlayer($dead);
		$this->killChildren($dead);

		$this->combat->getStreaks()->resetStreak($dead, $killer);

		foreach($this->getAssistingPlayers($dead) as $assist){
			if($assist != $killer && $assist != $dead){
				$assist->addTechits(2);
				$assist->addGlobalExp(1);
				$assist->sendMessage(TextFormat::AQUA."Assist> ".TextFormat::GREEN."Earned 2 Techits for helping kill ".$dead->getName()."!");
				$this->lb->addAssist($assist);
			}
		}
		$this->unsetAssistingPlayers($dead);
		$this->setLastKiller($dead, $killer);
		if($this->getLastKiller($killer) == $dead->getName()){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
				$player->sendMessage(TextFormat::AQUA."Revenge> ".TextFormat::GREEN.$killer->getName()." got revenge against ".$dead->getName()."!");
			}
			$killer->addTechits(3);
			$killer->addGlobalExp(2);
			$this->unsetLastKiller($killer);
			$this->lb->addRevenge($killer);
		}

		$killer->sendMessage(TextFormat::AQUA."Slay> ".TextFormat::GREEN."You killed ".$dead->getName()." and earned 5 Techits!");
		$dead->sendMessage(TextFormat::AQUA."Slay> ".TextFormat::RED.$killer->getName()." killed you with ".($killer->getHealth() / 2)." <3's left!");
	}

	public function processSuicide(Player $player){
		$this->addDeath($player);
		$this->combat->getStreaks()->setStreak($player, 0);
		$this->combat->getLogging()->removeCombat($player);
		$this->combat->getBodies()->addBody($player);
		$this->plugin->getArena()->exitArena($player);

		$this->unsetAssistingPlayers($player);

		$this->unsetLastKiller($player);

		$this->killChildren($player);
		$this->resetPlayer($player);
	}

	public function damageAs(Player $damager, Player $victim, $damage){
		$ev = new EntityDamageByEntityEvent($damager, $victim, 1, $damage, 0);
		$victim->attack($ev);
	}

	public function resetPlayer(Player $player){
		$player->extinguish();
		$player->setHealth(20);
		$player->removeAllEffects();
		$player->getInventory()->clearAll();
	}

	public function strikeLightning(Position $pos){
		$pk = new AddEntityPacket();
		$pk->type = 93;
		$pk->entityRuntimeId = $eid = Entity::$entityCount++;
		$pk->position = $pos->asVector3();
		$pk->yaw = $pk->pitch = 0;
		foreach($pos->getLevel()->getPlayers() as $p){
			$p->dataPacket($pk);
		}
	}

	public function killChildren(Player $player){
		foreach($this->plugin->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if($entity instanceof Projectile){
					if($entity->getOwningEntity() == $player){
						$entity->close();
					}
				}
			}
		}
	}

	public function isInvincible(Player $player){
		return isset($this->invincible[strtolower($player->getName())]);
	}

	public function setInvincible(Player $player, $time = 10){
		$this->invincible[strtolower($player->getName())] = time() + $time;
		$player->sendMessage(TextFormat::AQUA."Invincibility> ".TextFormat::GREEN."You have ".$time." seconds of invincibility!");	
	}

	public function canRemoveInvincibility(Player $player){
		return $this->invincible[strtolower($player->getName())] - time() <= 0;
	}

	public function removeInvincibility(Player $player){
		unset($this->invincible[strtolower($player->getName())]);
	}

	public function getAssistingPlayers(Player $player){
		$players = [];
		if(!isset($this->assists[$player->getName()])) return;
		foreach($this->assists[$player->getName()] as $assist){
			$pl = $this->plugin->getServer()->getPlayerExact($assist);
			if($pl instanceof Player){
				$c = 0;
				foreach($players as $p){
					if($p == $pl) $c++;
				}
				if($c == 0) $players[] = $pl;
			}
		}
		return $players;
	}

	public function isAssistingPlayer(Player $victim, Player $hitter){
		return isset($this->assists[$victim->getName()][$hitter->getName()]);
	}

	public function addAssistingPlayer(Player $victim, Player $hitter){
		$this->assists[$victim->getName()][] = $hitter->getName();
	}

	public function unsetAssistingPlayers(Player $player){
		$this->assists[$player->getName()] = [];
		unset($this->assists[$player->getName()]);
	}

	public function setLastKiller(Player $victim, Player $killer){
		$this->lastkilledby[$victim->getName()] = $killer->getName();
	}

	public function getLastKiller(Player $player){
		return $this->lastkilledby[$player->getName()] ?? "";
	}

	public function unsetLastKiller(Player $player){
		unset($this->lastkilledby[$player->getName()]);
	}

}