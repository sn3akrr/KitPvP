<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;

use kitpvp\KitPvP;

class ThrownKunai extends Projectile{

	const NETWORK_ID = 39;

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
			$this->close();
			$hasUpdate = true;
		}
		if($this->age > 30){
			$this->kill();
			$hasUpdate = true;
		}
		return $hasUpdate;
	}

}