<?php namespace kitpvp\combat;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerJoinEvent,
	PlayerQuitEvent,
	PlayerItemConsumeEvent
};
use pocketmine\event\entity\{
	EntityRegainHealthEvent,
	EntityDamageEvent,
	EntityDamageByEntityEvent,
	EntityDamageByChildEntityEvent
};
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\block\Block;
use pocketmine\utils\TextFormat;
use pocketmine\item\Food;

use kitpvp\KitPvP;

use core\AtPlayer as Player;
use core\Core;

class EventListener implements Listener{

	public $plugin;
	public $combat;

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
	}

	public function onJoin(PlayerJoinEvent $e){
		$player = $e->getPlayer();
		$this->combat->onJoin($player);
	}

	public function onQuit(PlayerQuitEvent $e){
		$player = $e->getPlayer();
		$this->combat->onQuit($player);
	}

	public function onConsume(PlayerItemConsumeEvent $e){
		$player = $e->getPlayer();
		$item = $e->getItem();
		if($item instanceof Food){
			$e->setCancelled(true);
			$restore = $item->getFoodRestore();
			if($player->getHealth() + $restore >= $player->getMaxHealth()){
				$player->setHealth($player->getMaxHealth());
			}else{
				$player->setHealth($player->getHealth() + $restore);
			}
			$clone = clone $item;
			$clone->setCount($clone->getCount() - 1);
			$player->getInventory()->setItemInHand($clone);
			if(count($item->getAdditionalEffects()) > 0){
				foreach($item->getAdditionalEffects() as $effect){
					$player->addEffect($effect);
				}
			}
			$player->getAttributeMap()->getAttribute(7)->markSynchronized(false);
		}
	}

	public function onRegain(EntityRegainHealthEvent $e){
		$player = $e->getEntity();
		if($player instanceof Player){
			$cause = $e->getRegainReason();
			if($cause == EntityRegainHealthEvent::CAUSE_SATURATION){
				$e->setCancelled(true);
			}
		}
	}

	public function onDmg(EntityDamageEvent $e){
		if($e->isCancelled()) return;

		$player = $e->getEntity();
		$teams = $this->combat->getTeams();
		if(!$player instanceof Player) return;
		if($e->getCause() == EntityDamageEvent::CAUSE_FALL){
			$e->setCancelled(true);
			return;
		}
		if(!$this->plugin->getArena()->inArena($player)){
			$duels = $this->plugin->getDuels();
			if($duels->inDuel($player)){
				$duel = $duels->getPlayerDuel($player);
				if($duel->getGameStatus() == 0){
					$e->setCancelled(true);
					return;
				}
			}else{
				$e->setCancelled(true);
				return;
			}
		}
		$combat = $this->plugin->getCombat();
		if($combat->getSlay()->isInvincible($player)){
			$e->setCancelled(true);
			return;
		}

		if($e instanceof EntityDamageByEntityEvent){
			$player->getLevel()->addParticle(new DestroyBlockParticle($player, Block::get(152)));
			$killer = $e->getDamager();
			if($killer instanceof Player){
				if($teams->sameTeam($player, $killer)){
					$e->setCancelled(true);
					$killer->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::RED.$player->getName()." is on your team!");
					return;
				}
				if($combat->getSlay()->isDelayed($killer)){
					$e->setCancelled(true);
					return;
				}
				if($killer == $player){
					$e->setCancelled(true);
					return;
				}
				if($combat->getSlay()->isInvincible($killer)){
					$e->setCancelled(true);
					return;
				}
				if($e->getFinalDamage() >= $player->getHealth()){
					$e->setCancelled(true);
					$combat->getSlay()->processKill($killer, $player);
				}else{
					if(!$combat->getLogging()->inCombat($player)){
						$player->sendMessage(TextFormat::AQUA."Logging> ".TextFormat::GREEN."You are now in combat mode! Logging out will cause you to lose 10 Techits!");
					}
					if(!$combat->getLogging()->inCombat($killer)){
						$killer->sendMessage(TextFormat::AQUA."Logging> ".TextFormat::GREEN."You are now in combat mode! Logging out will cause you to lose 10 Techits!");
					}
					$combat->getLogging()->setCombat($player, $killer);
					$combat->getLogging()->setCombat($killer, $player);

					if(!$combat->getSlay()->isAssistingPlayer($player, $killer)) $combat->getSlay()->addAssistingPlayer($player, $killer);
				}
				if(!$e instanceof EntityDamageByChildEntityEvent && $killer->getDeviceOs() == 7) $combat->getSlay()->setDelay($killer);
			}
		}else{
			if($e->getFinalDamage() >= $player->getHealth()){
				$e->setCancelled(true);
				if($combat->getLogging()->inCombat($player)){
					$last = $combat->getLogging()->getLastHitter($player);
					if($last == null){
						if(!$duels->inDuel($player)) $combat->getSlay()->processSuicide($player);
					}else{
						$combat->getSlay()->processKill($last, $player);
					}
				}else{
					if(!$duels->inDuel($player)) $combat->getSlay()->processSuicide($player);
				}
			}
		}
	}

}