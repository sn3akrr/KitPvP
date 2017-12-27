<?php namespace kitpvp\combat\special\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;

use kitpvp\KitPvP;
use kitpvp\combat\special\items\ConcussionGrenade;

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

	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->closed){
			return false;
		}

		$hasUpdate = parent::entityBaseTick($tickDiff);

		if($this->age > 1200){
			$this->close();
			$hasUpdate = true;
		}
		$owner = $this->getOwningEntity();
		if(!$owner instanceof Player){
			$this->close();
			return true;
		}
		if(KitPvP::getInstance()->getArena()->inSpawn($owner)){
			$this->close();
			return true;
		}
		if($this->onGround or $this->isCollided){
			$special = KitPvP::getInstance()->getCombat()->getSpecial();
			foreach($this->getViewers() as $player){
				if($player->distance($this) <= 5 && $player != $this->getOwningEntity() && $player instanceof Player){
					$grenade = new ConcussionGrenade();
					$grenade->concuss($player, $this->getOwningEntity());
				}
				$this->close();
				$hasUpdate = true;
			}
		}
		return $hasUpdate;
	}

}