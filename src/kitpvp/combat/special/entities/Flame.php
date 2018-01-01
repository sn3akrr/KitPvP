<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\level\particle\EntityFlameParticle;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;

use kitpvp\KitPvP;

class Flame extends Projectile{

	const NETWORK_ID = 94;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.001;
	protected $drag = 0.01;

	public $lit = [];
	public $get = false;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		parent::__construct($level, $nbt, $shootingEntity);
	}

	public function onUpdate(int $currentTick) : bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::onUpdate($currentTick);

		$owner = $this->getOwningEntity();
		if(!$owner instanceof Player){
			$this->close();
			return true;
		}
		if(KitPvP::getInstance()->getArena()->inSpawn($owner)){
			$this->close();
			return true;
		}
		if($this->onGround or $this->isCollided or $this->distance($owner) >= 25){
			$this->close();
			return true;
		}

		if($this->getLevel() != null){
			foreach($this->getLevel()->getEntities() as $player){
				if($player != $owner && $player->distance($this) <= 4){
					$teams = KitPvP::getInstance()->getCombat()->getTeams();
					if(!$player instanceof Projectile || ($player instanceof Player && !$teams->sameTeam($player, $owner))){
						$player->setOnFire(2);
						KitPvP::getInstance()->getCombat()->getSlay()->damageAs($owner, $player, 0);
						if($player instanceof Player) if(!isset($this->lit[$player->getName()])) $this->lit[$player->getName()] = true;
						if(count($this->lit) >= 2 && $this->get == false){
							$this->get = true;
							$as = KitPvP::getInstance()->getAchievements()->getSession($owner);
							if(!$as->hasAchievement("multiple_flamethrower")) $as->get("multiple_flamethrower");
						}
					}
				}
			}
			for($i = 0; $i <= 2; $i++) $this->getLevel()->addParticle(new EntityFlameParticle($this->add(mt_rand(-10,10) / 10,mt_rand(-10,10) / 10,mt_rand(-10,10) / 10)));
		}
		return $hasUpdate;
	}

}