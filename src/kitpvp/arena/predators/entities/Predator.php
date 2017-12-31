<?php namespace kitpvp\arena\predators\entities;

use pocketmine\entity\Human;
use pocketmine\level\Level;
use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;
use pocketmine\block\Liquid;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\{
	Server,
	Player
};

use kitpvp\KitPvP;

class Predator extends Human{

	const FIND_DISTANCE = 15;
	const LOSE_DISTANCE = 25;

	public $target = "";
	public $findNewTargetTicks = 0;

	public $randomPosition = null;
	public $findNewPositionTicks = 300;

	public $stopped = false;
	public $nextMoveTick = 100;

	public $jumpTicks = 5;
	public $attackWait = 20;


	public $attackDamage = 4;
	public $speed = 0.35;
	public $startingHealth = 20;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);

		$this->setMaxHealth($this->startingHealth);
		$this->setHealth($this->startingHealth);

		$this->setNametag($this->getNametag());
		$this->generateRandomPosition();
	}

	public function isBoss(){
		return false;
	}

	public function getType(){
		return "Predator";
	}

	public function getNametag(){
		return TextFormat::RED . $this->getType() . " " . TextFormat::GREEN . "(" . $this->getHealth() . "/" . $this->getMaxHealth() . ")";
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);
		if(!$this->isAlive()){
			if(!$this->closed) $this->flagForDespawn();
			return false;
		}
		$this->setNametag($this->getNametag());

		if($this->hasTarget()){
			return $this->attackTarget();
		}

		if($this->findNewTargetTicks > 0){
			$this->findNewTargetTicks--;
		}
		if(!$this->hasTarget() && $this->findNewTargetTicks === 0){
			$this->findNewTarget();
		}

		if($this->jumpTicks > 0){
			$this->jumpTicks--;
		}
		if(!$this->findNewPositionTicks > 0){
			$this->findNewPositionTicks--;
		}
		if(!$this->nextMoveTick > 0){
			$this->nextMoveTick--;
		}

		if(!$this->isOnGround()){
			if($this->motionY > -$this->gravity * 4){
				$this->motionY = -$this->gravity * 4;
			}else{
				$this->motionY += $this->isInsideOfWater() ? $this->gravity : -$this->gravity;
			}
		}else{
			$this->motionY -= $this->gravity;
		}
		if($this->isCollidedHorizontally && $this->jumpTicks === 0) {
			$this->jump();
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		if($this->nextMoveTick === 0){
			if($this->stopped){
				$this->stopped = false;
			}else{
				$this->stopped = true;
			}
			$this->nextMoveTick = mt_rand(60,160);
		}
		if($this->stopped == true) return true;

		if($this->atRandomPosition() || $this->findNewPositionTicks === 0){
			$this->generateRandomPosition();
			$this->findNewPositionTicks = 300;
			return true;
		}

		$position = $this->getRandomPosition();
		$x = $position->x - $this->getX();
		$y = $position->y - $this->getY();
		$z = $position->z - $this->getZ();

		$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
		$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));

		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$this->updateMovement();
		return $this->isAlive();
	}

	public function attackTarget(){
		$target = $this->getTarget();
		if($target == null || $target->distance($this) >= self::LOSE_DISTANCE){
			$this->target = null;
			return true;
		}

		if($this->jumpTicks > 0) {
			$this->jumpTicks--;
		}

		if(!$this->isOnGround()) {
			if($this->motionY > -$this->gravity * 4){
				$this->motionY = -$this->gravity * 4;
			}else{
				$this->motionY += $this->isInsideOfWater() ? $this->gravity : -$this->gravity;
			}
		}else{
			$this->motionY -= $this->gravity;
		}
		if($this->isCollidedHorizontally && $this->jumpTicks === 0){
			$this->jump();
		}
		$this->move($this->motionX, $this->motionY, $this->motionZ);

		$x = $target->x - $this->x;
		$y = $target->y - $this->y;
		$z = $target->z - $this->z;

		if($x * $x + $z * $z < 1.2){
			$this->motionX = 0;
			$this->motionZ = 0;
		} else {
			$this->motionX = $this->getSpeed() * 0.15 * ($x / (abs($x) + abs($z)));
			$this->motionZ = $this->getSpeed() * 0.15 * ($z / (abs($x) + abs($z)));
		}
		$this->yaw = rad2deg(atan2(-$x, $z));
		$this->pitch = rad2deg(-atan2($y, sqrt($x * $x + $z * $z)));

		$this->move($this->motionX, $this->motionY, $this->motionZ);

		if($this->distance($target) <= 1.1 && $this->attackWait <= 0){
			$event = new EntityDamageByEntityEvent($this, $target, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->getBaseAttackDamage());
			if($target->getHealth() - $event->getFinalDamage() <= 0){
				KitPvP::getInstance()->getCombat()->getSlay()->processKill($this, $target);
				$this->target = null;
				$event->setCancelled(true);
			}
			$this->broadcastEntityEvent(4);
			$target->attack($event);
			$this->attackWait = 20;
		}

		$this->updateMovement();
		$this->attackWait--;
		return $this->isAlive();
	}

	public function attack(EntityDamageEvent $source){
		parent::attack($source);

		if($source instanceof EntityDamageByEntityEvent){
			$killer = $source->getDamager();
			if($killer instanceof Player){
				if($this->target != $killer->getName()){
					$this->target = $killer->getName();
				}
			}
		}
	}

	public function kill(){
		if($this->getTarget() != null) KitPvP::getInstance()->getCombat()->getSlay()->processKill($this->getTarget(), $this);
		parent::kill();
	}

	public function spawnToAll(){
		parent::spawnToAll();
		if($this->isBoss()){
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "A " . TextFormat::YELLOW . $this->getType() . TextFormat::GRAY . " has spawned! Kill it for a big prize.");
			}
		}
	}

	//Targetting//
	public function findNewTarget(){
		$distance = self::FIND_DISTANCE;
		$target = null;
		foreach($this->getLevel()->getPlayers() as $player){
			if($player->distance($this) <= $distance){
				$distance = $player->distance($this);
				$target = $player;
			}
		}
		$this->findNewTargetTicks = 60;
		$this->target = ($target != null ? $target->getName() : "");
	}

	public function hasTarget(){
		return $this->getTarget() != null;
	}

	public function getTarget(){
		return Server::getInstance()->getPlayerExact((string) $this->target);
	}

	public function atRandomPosition(){
		return $this->getRandomPosition() == null || $this->distance($this->getRandomPosition()) <= 2;
	}

	public function getRandomPosition(){
		return $this->randomPosition;
	}

	public function generateRandomPosition(){
		$minX = $this->getFloorX() - 8;
		$minY = $this->getFloorY() - 8;
		$minZ = $this->getFloorZ() - 8;

		$maxX = $minX + 16;
		$maxY = $minY + 16;
		$maxZ = $minZ + 16;

		$level = $this->getLevel();

		for($attempts = 0; $attempts < 16; ++$attempts){
			$x = mt_rand($minX, $maxX);
			$y = mt_rand($minY, $maxY);
			$z = mt_rand($minZ, $maxZ);
			while($y >= 0 and !$level->getBlockAt($x, $y, $z)->isSolid()){
				$y--;
			}
			if($y < 0){
				continue;
			}
			$blockUp = $level->getBlockAt($x, $y + 1, $z);
			$blockUp2 = $level->getBlockAt($x, $y + 2, $z);
			if($blockUp->isSolid() or $blockUp instanceof Liquid or $blockUp2->isSolid() or $blockUp2 instanceof Liquid){
				continue;
			}

			break;
		}

		$this->randomPosition = new Vector3($x, $y + 1, $z);
	}

	public function getSpeed(){
		return $this->speed;
	}

	public function getBaseAttackDamage(){
		return $this->attackDamage;
	}

	public function jump(){
		$this->motionY = $this->gravity * 8;
		$this->move($this->motionX, $this->motionY, $this->motionZ);
		$this->jumpTicks = 5;
	}

}
