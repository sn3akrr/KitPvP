<?php namespace kitpvp\combat\slay;

use pocketmine\level\Position;
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\{
	AddEntityPacket,
	RemoveEntityPacket
};
use pocketmine\item\Food;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\entity\{
	Entity,
	Projectile
};
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;
use kitpvp\arena\predators\entities\Predator;

use core\Core;

class Slay{

	public $plugin;
	public $combat;
	public $lb;

	public $invincible = [];

	public $lastkilledby = [];
	public $assists = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
		$this->lb = $plugin->getLeaderboard();
	}

	public function tick(){
		foreach($this->invincible as $name => $time){
			$player = $this->plugin->getServer()->getPlayerExact($name);
			if($player instanceof Player){
				if($this->canRemoveInvincibility($player)){
					$this->removeInvincibility($player);
					$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You are no longer invincible.");	
				}
			}else{
				unset($this->invincibility[$name]);
			}
		}
	}

	public function addKill(Player $player){
		$this->lb->addKill($player);

		$teams = $this->combat->getTeams();
		if($teams->inTeam($player)){
			$teams->getPlayerTeam($player)->addKill();
		}

		$kills = $this->lb->getKills($player);
		$as = KitPvP::getInstance()->getAchievements()->getSession($player);
		if($kills >= 1){
			if(!$as->hasAchievement("kills_1")){
				$as->get("kills_1");
			}
		}
		if($kills >= 5){
			if(!$as->hasAchievement("kills_2")){
				$as->get("kills_2");
			}
		}
		if($kills >= 10){
			if(!$as->hasAchievement("kills_3")){
				$as->get("kills_3");
			}
		}
		if($kills >= 25){
			if(!$as->hasAchievement("kills_4")){
				$as->get("kills_4");
			}
		}
		if($kills >= 50){
			if(!$as->hasAchievement("kills_5")){
				$as->get("kills_5");
			}
		}
		if($kills >= 100){
			if(!$as->hasAchievement("kills_6")){
				$as->get("kills_6");
			}
		}
		if($kills >= 250){
			if(!$as->hasAchievement("kills_7")){
				$as->get("kills_7");
			}
		}
		if($kills >= 500){
			if(!$as->hasAchievement("kills_8")){
				$as->get("kills_8");
			}
		}
		if($kills >= 750){
			if(!$as->hasAchievement("kills_9")){
				$as->get("kills_9");
			}
		}
		if($kills >= 1000){
			if(!$as->hasAchievement("kills_10")){
				$as->get("kills_10");
			}
		}
	}

	public function addDeath(Player $player){
		$this->lb->addDeath($player);

		$teams = $this->combat->getTeams();
		if($teams->inTeam($player)){
			$teams->getPlayerTeam($player)->addDeath();
		}
	}

	public function processKill(Entity $killer, Entity $dead){
		if($killer instanceof Player){
			if($dead instanceof Player){
				$duels = $this->plugin->getDuels();
				if(!$duels->inDuel($dead)){
					$this->addKill($killer);
					$this->combat->getStreaks()->addStreak($killer);
					$this->combat->getLogging()->removeCombat($killer);
					$killer->addTechits(5);

					$teams = KitPvP::getInstance()->getCombat()->getTeams();
					if($teams->inTeam($killer)){
						$team = $teams->getPlayerTeam($killer);
						$opposite = $team->getOppositeMember($killer);
						if($this->getLastKiller($opposite) == $dead->getName()){
							$as = KitPvP::getInstance()->getAchievements()->getSession($killer);
							if(!$as->hasAchievement("team_3")){
								$as->hasAchievement("team_3");
							}
						}
					}

					if($killer->getHealth() <= 4){
						$as = KitPvP::getInstance()->getAchievements()->getSession($killer);
						if(!$as->hasAchievement("close_call")) $as->get("close_call");
					}
					if($killer->getHealth() == $killer->getMaxHealth()){
						$as = KitPvP::getInstance()->getAchievements()->getSession($killer);
						if(!$as->hasAchievement("perfect")) $as->get("perfect");
					}

					$streak = $this->combat->getStreaks()->getStreak($dead);
					if(!isset($streak) || $streak == 0){
						$as = KitPvP::getInstance()->getAchievements()->getSession($dead);
						$ks = KitPvP::getInstance()->getKits()->getSession($dead);
						if($ks->hasKit()){
							$kit = $ks->getKit()->getName();
							switch($kit){
								case "noob":
									if(!$as->hasAchievement("lol_noob")) $as->get("lol_noob");
								break;
								default:
									if(!$as->hasAchievement("wasted")) $as->get("wasted");
								break;
							}
						}
					}

					$this->strikeLightning($dead);
					$this->addDeath($dead);
					$this->combat->getLogging()->removeCombat($dead);
					$this->combat->getBodies()->addBody($dead);
					foreach($dead->getInventory()->getContents() as $item){
						if($item instanceof Food){
							$dead->getLevel()->dropItem($dead, $item);
							break;
						}
					}
					$this->plugin->getArena()->exitArena($dead);
					$this->resetPlayer($dead);
					$this->killChildren($dead);

					$this->combat->getStreaks()->resetStreak($dead, $killer);

					foreach($this->getAssistingPlayers($dead) as $assist){
						if($assist != $killer && $assist != $dead){
							$assist->addTechits(2);
							$assist->addGlobalExp(1);
							$assist->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Earned " . TextFormat::AQUA . "2 Techits" . TextFormat::GRAY . " for helping kill " . TextFormat::RED . $dead->getName()."!");
						}
					}
					$this->unsetAssistingPlayers($dead);
					$this->setLastKiller($dead, $killer);
					if($this->getLastKiller($killer) == $dead->getName()){
						foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
							$player->sendMessage(TextFormat::RED . TextFormat::BOLD . ">> " . TextFormat::RESET . TextFormat::YELLOW . $killer->getName(). TextFormat::GRAY . " got revenge against " . TextFormat::YELLOW . $dead->getName() . "!");
						}
						$killer->addTechits(3);
						$killer->addGlobalExp(2);
						$this->unsetLastKiller($killer);
					}

					$killer->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You killed " . TextFormat::RED . $dead->getName() . TextFormat::GRAY . " and earned " . TextFormat::AQUA . "5 Techits!");
					$dead->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::YELLOW . $killer->getName(). TextFormat::GRAY . " killed you with " . TextFormat::AQUA . ($killer->getHealth() / 2). TextFormat::RED . " <3's". TextFormat::GRAY . " left!");
				}else{
					$duel = $duels->getPlayerDuel($dead);
					$duel->setWinner($killer);
					$duel->setLoser($dead);
					$duel->end();
				}
			}elseif($dead instanceof Predator){
				$killer->sendMessage("you killed a ".$dead->getType()."!");
			}elseif($dead instanceof Envoy){

			}
		}elseif($killer instanceof Predator){
			if($dead instanceof Player){
				$dead->sendMessage("died to a ".$killer->getType()."!");

				$this->strikeLightning($dead);
				$this->addDeath($dead);
				$this->combat->getLogging()->removeCombat($dead);
				$this->combat->getBodies()->addBody($dead);
				foreach($dead->getInventory()->getContents() as $item){
					if($item instanceof Food){
						$dead->getLevel()->dropItem($dead, $item);
						break;
					}
				}
				$this->plugin->getArena()->exitArena($dead);
				$this->resetPlayer($dead);
				$this->killChildren($dead);

				$this->combat->getStreaks()->resetStreak($dead, $killer);

				foreach($this->getAssistingPlayers($dead) as $assist){
					if($assist != $killer && $assist != $dead){
						$assist->addTechits(2);
						$assist->addGlobalExp(1);
						$assist->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Earned " . TextFormat::AQUA . "2 Techits" . TextFormat::GRAY . " for helping kill " . TextFormat::RED . $dead->getName()."!");
					}
				}
			}
		}

	}

	public function processSuicide(Player $player){
		$this->addDeath($player);
		$this->combat->getStreaks()->setStreak($player, 0);
		$this->combat->getLogging()->removeCombat($player);
		$this->combat->getBodies()->addBody($player);
		foreach($player->getInventory()->getContents() as $item){
			if($item instanceof Food){
				$player->getLevel()->dropItem($player, $item);
				break;
			}
		}
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
		return isset($this->invincible[$player->getName()]);
	}

	public function setInvincible(Player $player, $time = 10){
		$this->invincible[$player->getName()] = time() + $time;
		$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You have ". TextFormat::WHITE . $time . TextFormat::GRAY . " seconds of invincibility!");	
	}

	public function canRemoveInvincibility(Player $player){
		return $this->invincible[$player->getName()] - time() <= 0;
	}

	public function removeInvincibility(Player $player){
		unset($this->invincible[$player->getName()]);
	}

	public function getAssistingPlayers(Player $player){
		$players = [];
		if(!isset($this->assists[$player->getName()])) return [];
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