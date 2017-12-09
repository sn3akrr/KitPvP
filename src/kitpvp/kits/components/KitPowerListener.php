<?php namespace kitpvp\kits\components;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerMoveEvent,
	PlayerQuitEvent
};
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent,
	EntityDamageByChildEntityEvent,
	EntityShootBowEvent
};
use pocketmine\network\mcpe\protocol\PlayerActionPacket;

use pocketmine\entity\{
	Entity,
	Effect,
	Arrow
};
use pocketmine\level\sound\{
	EndermanTeleportSound
};
use pocketmine\item\Item;

use kitpvp\KitPvP;
use kitpvp\kits\Kits;
use kitpvp\kits\event\{
	KitEquipEvent,
	KitUnequipEvent,
	KitReplenishEvent
};

use core\AtPlayer as Player;

class KitPowerListener implements Listener{

	public $plugin;
	public $kits;

	public function __construct(KitPvP $plugin, Kits $kits){
		$this->plugin = $plugin;
		$this->kits = $kits;
	}

	public function onEquip(KitEquipEvent $e){
		$player = $e->getPlayer();
		$kit = $e->getKit();

		if($kit->getName() == "m4l0ne23"){
			$player->setMaxHealth(24);
		}
		$player->setHealth($player->getMaxHealth());

		if($kit->getName() == "scout"){
			$player->setAllowFlight(true);
		}

		foreach($this->kits->kits as $kits){
			if($kits != $kit){
				$kits->subtractPlayerCooldown($player);
			}
		}
	}

	public function onUnequip(KitUnequipEvent $e){
		$player = $e->getPlayer();
		$player->setMaxHealth(20);
		$player->setGamemode(1); $player->setGamemode(0);

		unset($this->kits->ability[$player->getName()]);
		unset($this->plugin->getCombat()->getSpecial()->special[$player->getName()]);

		$this->kits->setInvisible($player, false); //check might make invalid..?
	}

	public function onReplenish(KitReplenishEvent $e){
		$player = $e->getPlayer();
		$kit = $e->getKit();
	}

	// Powers and shit below \\
	public function onMove(PlayerMoveEvent $e){
		$player = $e->getPlayer();
		$from = $e->getFrom();
		$to = $e->getTo();
		if($this->kits->hasKit($player)){
			$kit = $this->kits->getPlayerKit($player);
			switch($kit->getName()){
				case "spy":
					//Stealth Mode
					if(isset($this->kits->ability[$player->getName()]["still"])){
						if($player->getFloorX() != $this->kits->ability[$player->getName()]["still"][1] || $player->getFloorZ() != $this->kits->ability[$player->getName()]["still"][3]){
							unset($this->kits->ability[$player->getName()]["still"]);
							if(!$player->isSneaking()){
								if($this->kits->isInvisible($player)){
									$this->kits->setInvisible($player, false);
								}
							}
						}
					}
				break;
			}
		}
	}

	public function onQuit(PlayerQuitEvent $e){
		$player = $e->getPlayer();
	}

	public function onData(DataPacketReceiveEvent $e){
		$player = $e->getPlayer();
		$packet = $e->getPacket();
		$kits = $this->kits;
		if($packet instanceof PlayerActionPacket){
			$action = $packet->action;
			if($kits->hasKit($player)){
				$kit = $kits->getPlayerKit($player);
				switch($kit->getName()){
					case "spy":
						switch($action){
							case PlayerActionPacket::ACTION_START_SNEAK:
								//Stealth Mode
								if(!$this->plugin->getCombat()->getLogging()->inCombat($player)){
									$kits->setInvisible($player, true);
								}
							break;
							case PlayerActionPacket::ACTION_STOP_SNEAK:
								//Stealth Mode
								if($kits->isInvisible($player)){
									$kits->setInvisible($player, false);
								}
							break;
						}
					break;
				}
			}
		}
	}

	public function onDmg(EntityDamageEvent $e){
		$player = $e->getEntity();
		$kits = $this->plugin->getKits();
		$teams = $this->plugin->getCombat()->getTeams();
		if($player instanceof Player){
			if((!$this->plugin->getArena()->inArena($player)) || $this->plugin->getCombat()->getSlay()->isInvincible($player)){
				$e->setCancelled(true);
				return;
			}
			if($e instanceof EntityDamageByEntityEvent){
				$killer = $e->getDamager();
				if($killer instanceof Player){
					if($teams->sameTeam($player, $killer)){
						$e->setCancelled(true);
						return;
					}
					if($kits->hasKit($player)){
						$kit = $kits->getPlayerKit($player);
						switch($kit->getName()){
							case "witch":
								//Curse
								$chance = mt_rand(1,100);
								if($chance <= 1){
									$killer->addEffect(Effect::getEffect(Effect::POISON)->setDuration(20 * 4)->setAmplifier(2));
								}
							break;
							case "spy":
								//Last Chance
								if(!isset($kits->ability[$player->getName()]["last_chance"])){
									if(($player->getHealth() - $e->getFinalDamage()) <= 5){
										$player->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 5));
										$kits->setInvisible($player, true);
										foreach($player->getLevel()->getPlayers() as $p){
											if($p->distance($player) <= 4 && $p != $player){
												$dv = $p->getDirectionVector();
												$p->knockback($p, 0 -$dv->x, -$dv->z, 0.8);
											}
										}
										$kits->ability[$player->getName()]["last_chance"] = time();
									}
								}else{
									if($kits->isInvisible($player)){
										$kits->setInvisible($player, false);
									}
								}
							break;
							case "scout":
								//Bounceback
								$chance = mt_rand(1,100);
								if($chance <= 25){
									$dv = $killer->getDirectionVector();
									$killer->knockback($killer, 0 -$dv->x, -$dv->z, 0.45);
								}
							break;
							case "assault":
								//Adrenaline
								if(!isset($kits->ability[$player->getName()]["adrenaline"])){
									if(($player->getHealth() - $e->getFinalDamage()) <= 5){
										$player->removeEffect(Effect::SPEED);
										$player->addEffect(Effect::getEffect(Effect::JUMP)->setAmplifier(2)->setDuration(20 * 10));
										$player->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(4)->setDuration(20 * 10));
										$player->setHealth(15);
										$kits->ability[$player->getName()]["adrenaline"] = time();
									}
								}
							break;
							case "medic":
								//Miracle
								if(!isset($kits->ability[$player->getName()]["miracle"])){
									if(($player->getHealth() - $e->getFinalDamage()) <= 5){
										$player->setHealth($player->getHealth() + 5);
										$kits->ability[$player->getName()]["miracle"] = true;
									}
								}
							break;
							case "enderman":
								//Slender
								if(!isset($kits->ability[$player->getName()]["slender"])){
									if(($player->getHealth() - $e->getFinalDamage()) <= 5){
										$player->addEffect(Effect::getEffect(Effect::INVISIBILITY)->setDuration(20 * 5));
										$player->getLevel()->addSound(new EndermanTeleportSound($player));
										foreach($player->getLevel()->getPlayers() as $p){
											if($p->distance($player) <= 4 && $p != $player){
												$dv = $p->getDirectionVector();
												$p->knockback($p, 0 -$dv->x, -$dv->z, 0.8);
												$p->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 7));
											}
										}
										$kits->ability[$player->getName()]["slender"] = time();
									}
								}
								//Arrow Dodge
								if($e instanceof EntityDamageByChildEntityEvent){
									$child = $e->getChild();
									if($child instanceof Arrow){
										$chance = mt_rand(0,100);
										if($chance <= 25){
											$e->setCancelled();
											$player->getLevel()->addSound(new EndermanTeleportSound($player));
											$kits->setInvisible($player, true);
										}
									}
								}
							break;
							case "m4l0ne23":
								//Bounceback
								$chance = mt_rand(1,100);
								if($chance <= 25){
									$dv = $killer->getDirectionVector();
									$killer->knockback($killer, 0 -$dv->x, -$dv->z, 0.45);
								}
							break;
						}
					}
					if($kits->hasKit($killer)){
						$kit = $kits->getPlayerKit($killer);
						switch($kit->getName()){
							case "spy":
								//Stealth Mode
								if($kits->isInvisible($killer)){
									$kits->setInvisible($killer, false);
								}
							break;
							case "medic":
								//Life Steal
								if($e->getFinalDamage() >= $player->getHealth()){
									$killer->setHealth(($killer->getHealth() + 5 >= $killer->getMaxHealth() ? $killer->getMaxHealth() : $killer->getHealth() + 5));
								}
							break;
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
		if($this->kits->hasKit($player)){
			$kit = $this->kits->getPlayerKit($player);
			//Aim Assist
			if($kit->getName() == "archer"){
				if(isset($this->kits->ability[$player->getName()]["aim_assist"])){
					unset($this->kits->ability[$player->getName()]["aim_assist"]);
				}
			}
		}
	}

	public function onDp(DataPacketReceiveEvent $e){
		$player = $e->getPlayer();
		$packet = $e->getPacket();
		if($this->plugin->getArena()->inArena($player) && (!$this->plugin->getCombat()->getSlay()->isInvincible($player))){
			if($this->kits->hasKit($player)){
				$kit = $this->kits->getPlayerKit($player);
			}
		}
	}
}