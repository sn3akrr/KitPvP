<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;

use kitpvp\KitPvP;

class ThrownEnderpearl extends Projectile{

	const NETWORK_ID = 87;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.03;
	protected $drag = 0.01;

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
		if(!KitPvP::getInstance()->getArena()->inArena($owner)){
			$duels = KitPvP::getInstance()->getDuels();
			if($duels->inDuel($owner)){
				$duel = $duels->getPlayerDuel($owner);
				if($duel->getGameStatus() == 0){
					$this->close();
					return true;
				}
			}else{
				$this->close();
				return true;
			}
		}
		if($this->isCollided or $this->onGround){
			$owner->teleport($this);
			$this->close();
			$hasUpdate = true;
		}
		if($this->age > 1200){
			$this->kill();
			$hasUpdate = true;
		}
		return $hasUpdate;
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = ThrownEnderpearl::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}

}