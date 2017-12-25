<?php namespace kitpvp\kits\components;

use pocketmine\scheduler\PluginTask;
use pocketmine\level\{
	sound\GhastShootSound,

	particle\FlameParticle
};
use pocketmine\entity\Effect;
use pocketmine\item\Bow;
use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;

class KitPowerTask extends PluginTask{

	public $plugin;

	public function __construct(KitPvP $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->runs = 0;
	}

	public function onRun(int $currentTick){
		$this->runs++;
		$kits = $this->plugin->getKits();
		foreach($kits->sessions as $name => $session){
			$player = $session->getPlayer();
			if($player instanceof Player && $session->hasKit()){
				$kitname = $session->getKit()->getName();
				switch($kitname){
					case "scout":
						//Double Jump
						if($player->isFlying()){
							$player->setGamemode(1); $player->setGamemode(0);
							$dv = $player->getDirectionVector();
							$player->knockback($player, 0, $dv->x, $dv->z, 0.7);
							$player->getLevel()->addSound(new GhastShootSound($player));
							$session->ability["double_jump"] = time() + 5;
							$player->addActionBarMessage(TextFormat::RED."Recharging double jump... 5");
						}
						if(isset($session->ability["double_jump"])){
							if($session->ability["double_jump"] <= time()){
								if($player->getLevel()->getBlockIdAt($player->x, $player->y - 0.5, $player->z) != 0){
									unset($session->ability["double_jump"]);
									$player->setAllowFlight(true);
									$player->addActionBarMessage(TextFormat::GREEN."Double jump recharged.");
								}
							}else{
								$player->setAllowFlight(false);
								$player->setFlying(false);
								$time = $session->ability["double_jump"] - time();
								$player->addActionBarMessage(TextFormat::RED."Recharging double jump... ".$time);
							}
						}
					break;
					case "assault":
						//Adrenaline
						if(isset($session->ability["adrenaline"])){
							if(($session->ability["adrenaline"] + 10) - time() == 0){
								$player->removeEffect(Effect::SPEED);
								$player->addEffect(Effect::getEffect(Effect::SPEED)->setAmplifier(1)->setDuration(20 * 999999));
							}
						}
					break;
					case "archer":
						$hand = $player->getInventory()->getItemInHand();
						if($hand instanceof Bow && $player->getItemUseDuration() != -1){
							if(!isset($session->ability["aim_assist"])){
								$session->ability["aim_assist"] = ["time" => time()];
							}
							if(!isset($session->ability["aim_assist"]["target"])){
								$newtarget = [
									"dist" => 20,
									"player" => null
								];
								foreach($player->getLevel()->getPlayers() as $p){
									$teams = $this->plugin->getCombat()->getTeams();
									if($p != $player && $player->distance($p) <= $newtarget["dist"] && (!$teams->sameTeam($player, $p))){
										$newtarget["dist"] = $player->distance($p);
										$newtarget["player"] = $p;
									}
									if($newtarget["player"] != null){
										$session->ability["aim_assist"]["target"] = $newtarget["player"];
									}
								}
							}else{
								if(!isset($session->ability["aim_assist"]["targetted"])){
									$target = $session->ability["aim_assist"]["target"];
									if((!$target instanceof Player) || (!$this->plugin->getArena()->inArena($target)) || $target->distance($player) > 20){
										unset($session->ability["aim_assist"]["target"]);
									}else{
										$x = $player->x - $target->x;
										$y = $player->y - $target->y;
										$z = $player->z - $target->z;
										$yaw = asin($x / sqrt($x * $x + $z * $z)) / 3.14 * 180;
										$pitch = round(asin($y / sqrt($x * $x + $z * $z + $y * $y)) / 3.14 * 180);
										if($z > 0) $yaw = -$yaw + 180;

										$player->teleport($player, $yaw, $pitch + 0.25);

										$session->ability["aim_assist"]["targetted"] = true;
									}
								}
							}
						}else{
							if(isset($session->ability["aim_assist"])){
								unset($session->ability["aim_assist"]);
							}
						}
					break;
				}
			}else{
				unset($this->equipped[$name]);
			}
		}
		if($this->runs %20 == 0){
			foreach($kits->sessions as $name => $session){
				$player = $session->getPlayer();
				if($player instanceof Player && $session->hasKit()){
					$kitname = $session->getKit()->getName();
					switch($kitname){
						case "spy":
							if(!isset($session->ability["still"])){
								$session->ability["still"] = [time(),$player->getFloorX(),$player->getFloorY(),$player->getFloorZ()];
							}else{
								$time = $session->ability["still"][0];
								if(($time + 3) - time() <= 0){
									if(!$kits->isInvisible($player)){
										$kits->setInvisible($player, true);
									}
								}
							}
							if(isset($session->ability["last_chance"])){
								if(($session->ability["last_chance"] + 5) - time() == 0){
									if($kits->isInvisible($player)){
										$kits->setInvisible($player, false);
									}
								}
							}
						break;
						case "medic":
							if($this->runs %100 == 0){
								//Regen shit
								if($player->getHealth() != $player->getMaxHealth()){
									if($player->getHealth() + 2 > $player->getMaxHealth()){
										$player->setHealth($player->getMaxHealth());
									}else{
										$player->setHealth($player->getHealth() + 2);
									}
								}
							}
						break;
						case "pyromancer":
							if($this->runs %40 == 0){
								//Fire Aura
								if($this->plugin->getArena()->inArena($player)){
									$dmg = false;			
									foreach($player->getLevel()->getPlayers() as $p){
										if($p->distance($player) <= 6 && $p != $player){
											if($p->getHealth() - 2 <= 0){}else{
												$dmg = true;
												$this->plugin->getCombat()->getSlay()->damageAs($player, $p, 2);
												for($i = 0; $i <= 5; $i++){
													$p->getLevel()->addParticle(new FlameParticle($p->add((mt_rand(-10,10)/10),(mt_rand(0,20)/10),(mt_rand(-10,10)/10))));
												}
											}
										}
									}
									if($dmg){
										for($i = 0; $i <= 5; $i++){
											$player->getLevel()->addParticle(new FlameParticle($player->add((mt_rand(-10,10)/10),(mt_rand(0,20)/10),(mt_rand(-10,10)/10))));
										}
										$dmg = false;
									}

									if(!$player->hasEffect(Effect::SLOWNESS)){
										$player->addEffect(Effect::getEffect(Effect::SLOWNESS)->setDuration(10000 * 10000));
									}
								}
							}
						break;
					}
				}else{
					if($player instanceof Player){
						if($kits->isInvisible($player)){
							$kits->setInvisible($player, false);
						}
						$session->resetAbilityArray();
					}
				}
			}
		}
	}

}