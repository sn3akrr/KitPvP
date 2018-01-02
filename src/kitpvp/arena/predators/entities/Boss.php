<?php namespace kitpvp\arena\predators\entities;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\entity\Skin;

use pocketmine\Server;
use pocketmine\utils\TextFormat;

class Boss extends Predator{

	public $startingHealth = 250;

	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		$this->setScale(1.5);
	}

	public function getNametag(){
		return TextFormat::YELLOW . TextFormat::BOLD . "[BOSS] " .  TextFormat::RESET . parent::getNametag();
	}

	public function isBoss(){
		return true;
	}

	public function whistle(){
		foreach($this->getLevel()->getNearbyEntities($this->getBoundingBox()->grow(15, 15, 15)) as $entity){
			if($entity instanceof Predator && $entity->canWhistle){
				$entity->target = $this->target;
				$entity->canWhistle = false;
			}
		}
	}

	public function spawnToAll(){
		parent::spawnToAll();
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "A " . TextFormat::DARK_PURPLE . $this->getType() . " Boss" . TextFormat::GRAY . " has spawned! Kill it for a big prize.");
		}
	}

}