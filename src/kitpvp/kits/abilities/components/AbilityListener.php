<?php namespace kitpvp\kits\abilities\components;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent,
	EntityDamageByChildEntityEvent,
	EntityShootBowEvent
};
use pocketmine\Player;

use pocketmine\entity\{
	Entity,
	Effect,
	Arrow
};
use pocketmine\item\Item;

use kitpvp\KitPvP;
use kitpvp\kits\Kits;
use kitpvp\kits\event\{
	KitUnequipEvent,
	KitReplenishEvent
};

class AbilityListener implements Listener{

	public $plugin;
	public $kits;

	public function __construct(KitPvP $plugin, Kits $kits){
		$this->plugin = $plugin;
		$this->kits = $kits;
	}

	public function onUnequip(KitUnequipEvent $e){
		$player = $e->getPlayer();
		$this->kits->setInvisible($player, false); //check might make invalid..?
	}

	public function onInt(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		if($e->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR && $player->getInventory()->getItemInHand()->getId() == Item::BOW){
			$session = $this->kits->getSession($player);
			if($session->hasKit()){
				$ab = $session->getKit()->getAbility("aim assist");
				if($ab != null){
					$ab->activate($player);
				}
			}
		}
	}

	// Powers and shit below \\
	public function onDmg(EntityDamageEvent $e){
		if($e->isCancelled()) return;

		$player = $e->getEntity();
		$kits = $this->plugin->getKits();
		$teams = $this->plugin->getCombat()->getTeams();
		if($player instanceof Player){
			if($e instanceof EntityDamageByEntityEvent){
				$killer = $e->getDamager();
				if($killer instanceof Player){
					$session = $this->kits->getSession($player);
					if($session->hasKit()){
						$kit = $session->getKit();
						$abilities = $kit->getAbilities();

						if($player->getHealth() - $e->getFinalDamage() <= 5){
							foreach($abilities as $ability){
								switch($ability->getName()){
									case "last chance":
									case "slender":
									case "adrenaline":
									case "miracle":
										if(!$ability->isUsed()){
											$ability->activate($player);
										}
									break;
								}
							}
						}
						foreach($abilities as $ability){
							switch($ability->getName()){
								case "bounceback":
									$chance = mt_rand(1,100);
									if($chance <= 25){
										$ability->activate($player, $killer);
									}
								break;
								case "curse":
									$chance = mt_rand(1,100);
									if($chance <= 1){
										$ability->activate($player, $killer);
									}
								break;
								case "arrow dodge":
									if($e instanceof EntityDamageByChildEntityEvent){
										$child = $e->getChild();
										if($child instanceof Arrow){
											$chance = mt_rand(0,100);
											if($chance <= 25){
												$ability->activate($player, $e);
											}
										}
									}
								break;
							}
						}
					}
					$session = $this->kits->getSession($killer);
					if($session->hasKit()){
						$kit = $session->getKit();
						$abilities = $kit->getAbilities();
						foreach($abilities as $ability){
							switch($ability->getName()){
								case "life steal":
									if($e->getFinalDamage() >= $player->getHealth()){
										$ability->activate($killer);
									}
								break;
							}
						}
					}
				}
			}
		}
	}

	public function onBow(EntityShootBowEvent $e){
		$player = $e->getEntity();
		$force = $e->getForce();
		$dv = $player->getDirectionVector();
		$session = $this->kits->getSession($player);
		if($session->hasKit()){
			$session->addBowShot();
			$shots = $session->getBowShots();
			if($shots >= 10){
				$as = $this->plugin->getAchievements()->getSession($player);
				if(!$as->hasAchievement("faker")) $as->get("faker");
			}
			$kit = $session->getKit();
			$ab = $kit->getAbility("aim assist");
			if($ab != null && $ab->isActive()){
				$ab->deactivate();
			}
		}
	}

}