<?php namespace kitpvp\arena\envoys\entities;

use pocketmine\event\entity\{
	EntityDamageEvent,
	EntityDamageByEntityEvent
};
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\MoveEntityPacket;
use pocketmine\entity\{
	Human,
	Skin,
	Entity
};

use kitpvp\KitPvP;
use kitpvp\arena\envoys\DropPoint;

class Envoy extends Human{

	const LIFESPAN = 300;

	public $dropPoint;

	public $lifetime = 0;
	public $ticks = 0;

	public $ny = 0;

	public $killer = null;

	public function __construct(Level $level, CompoundTag $nbt, DropPoint $dropPoint){
		parent::__construct($level, $nbt);

		$this->dropPoint = $dropPoint;
		$this->setCanSaveWithChunk(false);
		$this->setMaxHealth(50);
		$this->setHealth(50);
		$this->setNameTag($this->getNT());
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);

		$this->ny += 10;
		$pk = new MoveEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->getOffsetPosition($this);
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->headYaw = $this->ny;

		foreach($this->getViewers() as $viewer){
			$viewer->dataPacket($pk);
		}

		$this->ticks++;
		if($this->ticks % 20 != 0) return true;
		$this->lifetime++;

		$time = $this->getTimeLeft();
		if($time <= 0){
			$this->flagForDespawn();
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Envoy at " . TextFormat::LIGHT_PURPLE . $this->getDropPoint()->getName() . TextFormat::GRAY . " has despawned!");
			}
			return false;
		}
		$this->setNameTag($this->getNT());
		$this->setScale($this->getSettingScale());
		return true;
	}

	public function attack(EntityDamageEvent $source) : void{
		if($source instanceof EntityDamageByEntityEvent){
			$killer = $source->getDamager();
			if($killer instanceof Player){
				if(KitPvP::getInstance()->getArena()->getSpectate()->isSpectating($killer)){
					$source->setCancelled(true);
					return;
				}
				if(KitPvP::getInstance()->getCombat()->getSlay()->isInvincible($killer)){
					$source->setCancelled(true);
					return;
				}
			}
		}

		$this->setNameTag($this->getNT());
		parent::attack($source);
		if($source->getFinalDamage() >= $this->getHealth() && !$source->isCancelled()){
			if($source instanceof EntityDamageByEntityEvent){
				$killer = $source->getDamager();
				if($killer instanceof Player){
					$this->killer = $killer;
				}
			}
			$this->kill();
			$this->broadcastEntityEvent(3);
		}
	}

	public function kill() : void{
		parent::kill();
		$killer = $this->killer;
		if($killer instanceof Player){
			KitPvP::getInstance()->getCombat()->getSlay()->processKill($this->killer, $this);
		}else{
			foreach(Server::getInstance()->getOnlinePlayers() as $player){
				$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Envoy at " . TextFormat::LIGHT_PURPLE . $this->getDropPoint()->getName() . TextFormat::GRAY . " has despawned!");
			}
		}
	}

	public function getDrops() : array{
		return KitPvP::getInstance()->getArena()->getEnvoys()->getRandomItems();
	}

	public function knockBack(Entity $attacker, float $damage, float $x, float $z, $base = 0.4) : void{
		//Plz no knockback
	}

	public function getDropPoint(){
		return $this->dropPoint;
	}

	public function getLifetime(){
		return $this->lifetime;
	}

	public function getLifeSpan(){
		return self::LIFESPAN;
	}

	public function getTimeLeft(){
		return $this->getLifeSpan() - $this->getLifetime();
	}

	public function getTimeFormatted(){
		return gmdate("(i:s)", $this->getTimeLeft());
	}

	public function getSettingScale(){
		return 1;
		$scale = $this->getLifeSpan() / ($this->getLifeSpan() - $this->getLifetime());
		if($scale <= 0.5) return 0.5;
		return $scale;
	}

	public function getNT(){
		return TextFormat::RED . "Envoy " . TextFormat::GRAY . $this->getTimeFormatted() . " " . TextFormat::GREEN . $this->getHealth() . "/" . $this->getMaxHealth();
	}

}