<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;

class Bullet extends Projectile{

	const NETWORK_ID = 76;

	public $width = 0.2;
	public $length = 0.2;
	public $height = 0.2;

	protected $gravity = 0;
	protected $drag = 0.01;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		parent::__construct($level, $nbt, $shootingEntity);
	}

	public function onUpdate(int $currentTick) : bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->age > 1200 or $this->isCollided){
			$this->close();
			$hasUpdate = true;
		}

		if($this->onGround){
			$this->close();
			$hasUpdate = true;
		}
		return $hasUpdate;
	}

}