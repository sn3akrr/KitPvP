<?php namespace kitpvp\combat\special;

use pocketmine\event\Listener;
use pocketmine\event\player\{
	PlayerInteractEvent
};
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent,
	EntityDamageByChildEntityEvent
};
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\{
	PlayerActionPacket
};
use kitpvp\combat\special\event\{
	SpecialDelayEndEvent,

	SpecialEffectStartEvent,
	SpecialEffectEndEvent
};

use pocketmine\level\sound\{
	AnvilFallSound
};
use pocketmine\entity\{
	Living,
	Entity,
	Effect,
	Arrow
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;

use pocketmine\nbt\tag\{
	CompoundTag,
	ListTag,
	FloatTag,
	DoubleTag,
	ShortTag
};

use kitpvp\KitPvP;
use kitpvp\combat\special\entities\{
	ThrownConcussionGrenade,
	Bullet,
	ThrownEnderpearl,
	ThrownDecoy,
	ThrownKunai
};
use kitpvp\combat\special\items\Decoy;
use kitpvp\combat\special\items\ConcussionGrenade;
use kitpvp\arena\{
	envoys\entities\Envoy,
	predators\entities\Predator
};

class EventListener implements Listener{

	public $plugin;
	public $special;

	public function __construct(KitPvP $plugin, Special $special){
		$this->plugin = $plugin;
		$this->special = $special;
	}

	public function onInteract(PlayerInteractEvent $e){
		$player = $e->getPlayer();
		$teams = $this->plugin->getCombat()->getTeams();
		$item = $e->getItem();
		if($this->plugin->getArena()->inSpawn($player)){
			$e->setCancelled(true);
			return;
		}
		if($this->plugin->getCombat()->getSlay()->isInvincible($player)){
			$e->setCancelled(true);
			return;
		}
		$ticker = $this->special->getTickerByItem($item);
		if($ticker == null) return;
		if($ticker->hasCooldown($player)) return;
		switch($ticker->getName()){
			case "bookofspells":
				$count = 0;
				$spell = $this->special->getRandomSpell();
				foreach($player->getLevel()->getEntities() as $p){
					if($p instanceof Living){
						if($p->distance($player) <= 10 && $p != $player){
							if(!$p instanceof Player || (!$teams->sameTeam($player, $p) && $this->plugin->getArena()->getSpectate()->isSpectating($p))){
								$spell->cast($player, $p);
								$count++;
							}
						}
					}
				}
				if($count > 0){
					$ticker->use($player);
				}
			break;
			case "concussiongrenade":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$force = 0.4;
					$cg = Entity::createEntity("ThrownConcussionGrenade", $player->getLevel(), $nbt, $player);
					$cg->setMotion($cg->getMotion()->multiply($force));
					$cg->spawnToAll();
					$ticker->use($player);
				}
			break;
			case "kunai":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$force = 1.25;
					$kunai = Entity::createEntity("ThrownKunai", $player->getLevel(), $nbt, $player);
					$kunai->setMotion($kunai->getMotion()->multiply($force));
					$kunai->spawnToAll();
					$kunai->setDataProperty(38, 7, $player->getId());
					$kunai->setDataProperty(39, 3, 0.5);
					$ticker->use($player);
				}
			break;
			case "enderpearl":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$force = 1.6;
					$enderpearl= Entity::createEntity("ThrownEnderpearl", $player->getLevel(), $nbt, $player);
					$enderpearl->setMotion($enderpearl->getMotion()->multiply($force));
					$enderpearl->spawnToAll();
					$ticker->use($player);
				}
			break;
			case "decoy":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$force = 1.6;
					$decoy = Entity::createEntity("ThrownDecoy", $player->getLevel(), $nbt, $player);
					$decoy->setMotion($decoy->getMotion()->multiply($force));
					$decoy->spawnToAll();
					$ticker->use($player);
				}
			break;
			case "gun":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$force = 2.75;
					$bullet = Entity::createEntity("Bullet", $player->getLevel(), $nbt, $player);
					$bullet->setMotion($bullet->getMotion()->multiply($force));
					$bullet->spawnToAll();
					$ticker->use($player);
				}
			break;
			case "flamethrower":
				if($e->getAction() == 3){
					$nbt = $this->createNbt($player);
					$flame = Entity::createEntity("Flame", $player->getLevel(), $nbt, $player);
					$flame->spawnToAll();
					$ticker->use($player);
				}
			break;
		}
		if($item->isConsumable() && $e->getAction() == 3){
			$new = clone $item;
			$new->setCount($item->getCount() - 1);
			$player->getInventory()->setItemInHand($new);
		}
	}

	public function onDmg(EntityDamageEvent $e){
		if($e->isCancelled()) return;

		$player = $e->getEntity();
		$teams = $this->plugin->getCombat()->getTeams();
		if($player instanceof Living){
			if($player instanceof Player){
				if($this->plugin->getArena()->inSpawn($player)){
					$e->setCancelled(true);
					return;
				}
				if($this->plugin->getArena()->getSpectate()->isSpectating($player)){
					$e->setCancelled(true);
					return;
				}
			}
			if($e instanceof EntityDamageByEntityEvent){
				$killer = $e->getDamager();
				if($killer instanceof Living){
					$item = $killer->getInventory()->getItemInHand();
					if($e instanceof EntityDamageByChildEntityEvent){
						$child = $e->getChild();
						if($child instanceof ThrownConcussionGrenade){
							foreach($child->getLevel()->getEntities() as $entity){
								if($entity instanceof Living){
									if($entity->distance($child) <= 5 && $entity != $killer){
										$grenade = new ConcussionGrenade();
										$grenade->concuss($entity, $killer);
									}
								}
							}
						}
						if($child instanceof Bullet){
							if($killer instanceof Player){
								$e->setDamage(3);
								$e->setDamage(3, 4);
								if($player instanceof Predator){
									switch($player->getType()){
										case "Caveman":
										case "Jungleman":
	
										break;
										case "Cowboy":
											$as = $this->plugin->getAchievements()->getSession($killer);
											if(!$as->hasAchievement("this_town")) $as->get("this_town");
										break;
									}
								}
							}else{
								$e->setDamage(2);
							}
							if($player instanceof Player){
								$ks = $this->plugin->getKits()->getSession($player);
								if($ks->hasKit()){
									if($ks->getKit()->getName() == "archer"){
										if($killer instanceof Player){
											$as = $this->plugin->getAchievements()->getSession($killer);
											if(!$as->hasAchievement("archer_gun")) $as->get("archer_gun");
										}
									}
								}
							}

						}
						if($child instanceof ThrownKunai){
							$e->setDamage(mt_rand(1,6));
							$e->setDamage(mt_rand(1,6), 4);
							$dv = $killer->asVector3()->subtract($player->asVector3())->normalize();
							$player->knockback($killer, 0, $dv->x, $dv->z, 3);
						}
						if($killer instanceof Player){
							if($this->plugin->getArena()->getSpectate()->isSpectating($killer)){
								$e->setCancelled(true);
								return;
							}
							if($child instanceof ThrownEnderPearl){
								$killer->teleport($player);
							}
							if($child instanceof ThrownDecoy){
								$nt = $this->special->getTickerByItem(new Decoy());
								$nt->startEffect($killer);
							}
							if($child instanceof Arrow){
								$ks = $this->plugin->getKits()->getSession($killer);
								$ks->resetMissedShots();
							}
						}
						return;
					}

					$ticker = $this->special->getTickerByItem($item);
					if($ticker == null) return;
					if($ticker->hasCooldown($killer)) return;
					switch($ticker->getName()){
						case "fryingpan":
							$e->setKnockback(0.50);
							$e->setDamage(mt_rand(1,3));
							if(mt_rand(1,3) == 1){
								$player->getLevel()->addSound(new AnvilFallSound($player));
							}
						break;
						/*case "bookofspells":
							$spell = $this->special->getRandomSpell();
							if(!$teams->sameTeam($player, $killer)){
								$spell->cast($killer, $player);
								$ticker->use($killer, 10);
							}
						break;*/
						case "brassknuckles":
							$e->setKnockback(0.75);
							$e->setDamage(mt_rand(2,4));
							$e->setDamage(mt_rand(1,2), 4);
							if(mt_rand(1,3) == 1){
								$player->getLevel()->addSound(new AnvilFallSound($player));
							}
						break;
						case "reflexhammer":
							$e->setKnockback(0.65);
							$e->setDamage(mt_rand(2,3));
							$e->setDamage(mt_rand(1,2), 4);
						break;
						case "defibrillator":
							$ticker->use($killer);
							$this->plugin->getCombat()->getSlay()->strikeLightning($player);
							if($player instanceof Player) $player->addTitle(TextFormat::OBFUSCATED."KK".TextFormat::RESET.TextFormat::AQUA." CLEAR! ".TextFormat::OBFUSCATED."KK", TextFormat::YELLOW."ZAPPED!", 5, 20, 5);
							$killer->addTitle(TextFormat::OBFUSCATED."KK".TextFormat::RESET.TextFormat::AQUA." CLEAR! ".TextFormat::OBFUSCATED."KK", TextFormat::YELLOW."ZAPPED!", 5, 20, 5);
							$e->setDamage(3);
							$e->setDamage(2, 4);
							$player->addEffect(Effect::getEffect(Effect::SLOWNESS)->setDuration(20 * 10)->setAmplifier(1));
							$player->addEffect(Effect::getEffect(Effect::NAUSEA)->setDuration(20 * 10)->setAmplifier(5));
						break;
						case "syringe":
							$e->setDamage(5);
							$e->setDamage(2, 4);
							$player->addEffect(Effect::getEffect(Effect::NAUSEA)->setDuration(20 * 15));
							$player->addEffect(Effect::getEffect(Effect::POISON)->setAmplifier(1)->setDuration(20 * 5));
							$ticker->use($killer);
						break;
						case "spikedclub":
							$e->setDamage(mt_rand(2,3));
							$e->setDamage(mt_rand(1,2), 4);
							$e->setKnockback(0.65);
							if($player instanceof Player) $this->special->bleed($player, $killer, mt_rand(3,8));
						break;
						case "fireaxe":
							$e->setDamage(mt_rand(2,4));
							$e->setDamage(mt_rand(1,3), 4);
							$player->setOnFire(1);
						break;
						case "malonesword":
							$e->setKnockback(0.15);
							$e->setDamage(mt_rand(2,5));
							$e->setDamage(mt_rand(1,3), 4);
							$fire_chance = mt_rand(0,100);
							if($fire_chance <= 5){
								$player->setOnFire(1);
							}
							$wither_chance = mt_rand(0,100);
							if($wither_chance <= 2){
								$player->addEffect(Effect::getEffect(Effect::WITHER)->setAmplifier(1)->setDuration(20 * 2));
							}
						break;
					}
					if($item->isConsumable()){
						$new = clone $item;
						$new->setCount($item->getCount() - 1);
						$killer->getInventory()->setItemInHand($new);
					}
				}
			}
		}
	}

	public function onStart(SpecialEffectStartEvent $e){
		$player = $e->getPlayer();
		$special = $e->getSpecial();
		switch($special){
			case "decoy":
				$this->plugin->getKits()->setInvisible($player, true);
			break;
		}
	}

	public function onEnd(SpecialEffectEndEvent $e){
		$player = $e->getPlayer();
		$special = $e->getSpecial();
		switch($special){
			case "decoy":
				$this->plugin->getKits()->setInvisible($player, false);
			break;
		}
	}

	public function createNbt(Player $player){
		$aimPos = new Vector3(
			-sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI),
			-sin($player->pitch / 180 * M_PI),
			cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)
		);
		$nbt = new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $player->x),
				new DoubleTag("", $player->y + $player->getEyeHeight()),
				new DoubleTag("", $player->z)
			]),
			new ListTag("Motion", [
				new DoubleTag("", $aimPos->x),
				new DoubleTag("", $aimPos->y),
				new DoubleTag("", $aimPos->z)
			]),
			new ListTag("Rotation", [
				new FloatTag("", $player->yaw),
				new FloatTag("", $player->pitch)
			]),
		]);
		return $nbt;
	}

}