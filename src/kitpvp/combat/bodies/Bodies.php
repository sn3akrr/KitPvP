<?php namespace kitpvp\combat\bodies;

use pocketmine\network\mcpe\protocol\{
	AddPlayerPacket,
	MobArmorEquipmentPacket,
	PlayerSkinPacket,
	types\PlayerListEntry,
	RemoveEntityPacket
};
use pocketmine\utils\UUID;
use pocketmine\entity\{
	Entity,
	Human,
	Skin
};
use pocketmine\item\Item;
use pocketmine\math\Vector3;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;

use core\AtPlayer as Player;

class Bodies{

	const BODY_LIFESPAN = 60;

	public $plugin;
	public $combat;

	public $bodies = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;
	}

	public function tick(){
		foreach($this->bodies as $eid => $data){
			if($this->canDestroyBody($eid)){
				$this->destroyBody($eid);
			}
		}
	}

	public function addBody($thing, $players = []){
		if($thing instanceof Player){
			$eid = Entity::$entityCount++;
			$uuid = UUID::fromRandom();
			$skin = $thing->getSkin();
			$item = $thing->getInventory()->getItemInHand();
			$armor = $thing->getArmorInventory()->getContents();
			$x = (int) $thing->x;
			$y = (int) $thing->y;
			$z = (int) $thing->z;
			$pos = $thing->asVector3()->floor();
			$yaw = $thing->yaw;
			$pitch = $thing->pitch;
		}else{
			$eid = $thing;
			$uuid = $this->bodies[$eid]["uuid"];
			$skin = $this->bodies[$eid]["skin"];
			$item = $this->bodies[$eid]["item"];
			$armor = $this->bodies[$eid]["armor"];
			$x = (int) $this->bodies[$eid]["x"];
			$y = (int) $this->bodies[$eid]["y"];
			$z = (int) $this->bodies[$eid]["z"];
			$pos = new Vector3($this->bodies[$eid]["x"],$this->bodies[$eid]["y"],$this->bodies[$eid]["z"]);
			$yaw = $this->bodies[$eid]["yaw"];
			$pitch = $this->bodies[$eid]["pitch"];
		}

		$pk = new AddPlayerPacket();
		$pk->uuid = $uuid;
		$pk->username = "";
		$pk->entityRuntimeId = $eid;
		$pk->position = $pos;
		$pk->pitch = $pitch;
		$pk->headYaw = $pk->yaw = $yaw;
		$pk->item = $item;
		$flags = 0;
		$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
		$human_flags = 0;
		$human_flags |= 1 << Human::DATA_PLAYER_FLAG_SLEEP;
		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, ""],
			Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
			Human::DATA_PLAYER_FLAGS => [Human::DATA_TYPE_BYTE, $human_flags],
			Human::DATA_PLAYER_BED_POSITION => [Human::DATA_TYPE_POS, [$x, $y, $z]],
		];

		$pk2 = new MobArmorEquipmentPacket();
		$pk2->entityRuntimeId = $eid;
		$pk2->slots = $armor;

		$pk3 = new PlayerSkinPacket();
		$pk3->uuid = $uuid;
		$pk3->skin = $skin;

		if(empty($players)){
			foreach($this->plugin->getServer()->getOnlinePlayers() as $pl){
				if($pl->getLevel()->getName() == "KitArena"){
					$pl->dataPacket($pk);
					$pl->dataPacket($pk2);
					$pl->dataPacket($pk3);
				}
			}
			$this->bodies[$eid] = [
				"time" => time() + self::BODY_LIFESPAN, //heh, lifespan of a dead body
				"uuid" => $uuid,
				"skin" => $skin,
				"item" => $item,
				"armor" => $armor,
				"x" => $x,
				"y" => $y,
				"z" => $z,
				"yaw" => $yaw,
				"pitch" => $pitch,
			];
		}else{
			foreach($players as $pl){
				if($pl->getLevel()->getName() == "KitArena"){
					$pl->dataPacket($pk);
					$pl->dataPacket($pk2);
					$pl->dataPacket($pk3);
				}
			}
		}
	}

	public function canDestroyBody($eid){
		return $this->bodies[$eid]["time"] - time() <= 0;
	}

	public function destroyBody($eid){
		unset($this->bodies[$eid]);

		$pk = new RemoveEntityPacket();
		$pk->entityUniqueId = $eid;
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player) $player->dataPacket($pk);
	}

	public function removeAllBodies(Player $player){
		foreach($this->bodies as $eid => $data){
			$pk = new RemoveEntityPacket();
			$pk->entityUniqueId = $eid;
			$player->dataPacket($pk);
		}
	}

	public function addAllBodies(Player $player){
		foreach($this->bodies as $eid => $data){
			$this->addBody($eid, [$player]);
		}
	}
}