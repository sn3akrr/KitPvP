<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\entity\Entity;
use pocketmine\entity\Projectile;

use kitpvp\KitPvP;

class ThrownConcussionGrenade extends Projectile{

	const NETWORK_ID = 68;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0.05;
	protected $drag = 0.02;

	public function __construct(Level $level, CompoundTag $nbt, Entity $shootingEntity = null){
		parent::__construct($level, $nbt, $shootingEntity);
	}

	public function onUpdate(int $currentTick) : bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::onUpdate($currentTick);

		if($this->age > 1200){
			$this->close();
			$hasUpdate = true;
		}
		$owner = $this->getOwningEntity();
		if(!$owner instanceof Player){
			$this->close();
			return true;
		}
		if(!KitPvP::getInstance()->getArena()->inArena($owner)){
			$this->close();
			return false;
		}
		if($this->onGround or $this->isCollided){
			$special = KitPvP::getInstance()->getCombat()->getSpecial();
			foreach($this->getLevel()->getPlayers() as $player){
				if($player->distance($this) <= 5 && $player != $this->getOwningEntity()){
					$special->cg($player, $this->getOwningEntity());
				}
				$this->close();
				$hasUpdate = true;
			}
		}
		return $hasUpdate;
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket();
		$pk->type = ThrownConcussionGrenade::NETWORK_ID;
		$pk->entityRuntimeId = $this->getId();
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
		parent::spawnTo($player);
	}
}