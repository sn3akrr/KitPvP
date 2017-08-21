<?php namespace kitpvp\sparring;

use pocketmine\network\mcpe\protocol\{
	AddEntityPacket,
	AddPlayerPacket,
	EntityEventPacket,
	SetEntityDataPacket,
	RemoveEntityPacket
};
use pocketmine\utils\{
	UUID,
	TextFormat
};
use pocketmine\item\Item;
use pocketmine\entity\Entity;

use kitpvp\KitPvP;
use kitpvp\sparring\tasks\SparringTask;
use kitpvp\sparring\commands\Spar;

use core\AtPlayer as Player;

//TODO: Damage tracking

class Sparring{

	public $plugin;
	public $db;

	public $sparring = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;

		$this->db = new \SQLite3($plugin->dir . "sparring.db");
		foreach([
			"CREATE TABLE IF NOT EXISTS sparring(xuid TEXT PRIMARY KEY, most_hits INT, most_damage REAL)",
			"PRAGMA journal_mode = WAL"
		] as $statement) $this->db->exec($statement);

		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new SparringTask($plugin), 20);
		$plugin->getServer()->getCommandMap()->register("spar", new Spar($plugin, "spar", "Initiate a sparring session!"));
	}

	public function inDb(Player $player){
		$xuid = $player->getUniqueId();
		$query = $this->db->query("SELECT xuid FROM sparring WHERE xuid='$xuid'");
		$array = $query->fetchArray(SQLITE3_ASSOC);
		return !empty($array);
	}

	public function setDb(Player $player){
		$xuid = $player->getUniqueId();
		$this->db->exec("INSERT OR REPLACE INTO sparring(xuid, most_hits, most_damage) VALUES('$xuid', '0', '0')");
	}

	public function getMostHits(Player $player){
		$xuid = $player->getUniqueId();
		$query = $this->db->query("SELECT most_hits FROM sparring WHERE xuid='$xuid'");
		$array = $query->fetchArray(SQLITE3_ASSOC);
		return $array["most_hits"];
	}

	public function setMostHits(Player $player, $hits){
		$xuid = $player->getUniqueId();
		$damage = $this->getMostDamage($player);
		$this->db->exec("INSERT OR REPLACE INTO sparring(xuid, most_hits, most_damage) VALUES('$xuid', '$hits', '$damage')");
	}

	public function getMostDamage(Player $player){
		$xuid = $player->getUniqueId();
		$query = $this->db->query("SELECT most_damage FROM sparring WHERE xuid='$xuid'");
		$array = $query->fetchArray(SQLITE3_ASSOC);
		return $array["most_damage"];
	}

	public function setMostDamage(Player $player, $damage){
		$xuid = $player->getUniqueId();
		$hits = $this->getMostHits($player);
		$this->db->exec("INSERT OR REPLACE INTO sparring(xuid, most_hits, most_damage) VALUES('$xuid', '$hits', '$damage')");
	}

	public function onJoin(Player $player){
		$this->spawnAllSparringTargets($player);
	}

	public function onQuit(Player $player){
		if($this->isSparring($player)) $this->stopSpar($player);
	}

	public function spawnAllSparringTargets(Player $player){
		foreach($this->sparring as $name => $data){
			$eid = $data["eid"];
			$hits = $data["hits"]; $damage = $data["damage"];
			$x = $data["x"]; $y = $data["y"]; $z = $data["z"]; $yaw = $data["yaw"];

			$uuid = UUID::fromRandom();

			$pk = new AddPlayerPacket();
			$pk->uuid = $uuid;
			$pk->username = "Sparring Target";
			$pk->entityRuntimeId = $eid;
			$pk->x = $x;
			$pk->y = $y;
			$pk->z = $z;
			$pk->pitch = 0;
			$pk->headYaw = $yaw;
			$pk->yaw = $yaw;
			$pk->item = Item::get(0);
			$flags = 0;
			$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
			$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
			$flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
			$pk->metadata = [
				Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
				Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, TextFormat::AQUA.$name."'s Sparring Target\nHits: ".$hits."\nDamage: ".$damage],
				Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
			];
			$player->dataPacket($pk);
		}
	}

	public function despawnAllSparringTargets(Player $player){
		foreach($this->sparring as $name => $data){
			$pk = new RemoveEntityPacket();
			$pk->entityUniqueId = $data["eid"];
			$player->dataPacket($pk);
		}
	}

	public function startSpar(Player $player){
		$eid = Entity::$entityCount++;
		$this->sparring[$player->getName()] = [
			"eid" => $eid,
			"hits" => 0,
			"damage" => 0,
			"time" => time()
		];
		$this->spawnSparringTarget($player);
	}

	public function stopSpar(Player $player){
		$eid = $this->getEid($player);
		$hits = $this->getHits($player);
		$damage = $this->getDamage($player);

		if($hits > $this->getMostHits($player)){
			$this->setMostHits($player, $hits);
		}
		if($damage > $this->getMostDamage($player)){
			$this->setMostDamage($player);
		}

		unset($this->sparring[$player->getName()]);
		$this->despawnSparringTarget($player, $eid);
	}

	public function isSparring(Player $player){
		return isset($this->sparring[$player->getName()]);
	}

	public function spawnSparringTarget(Player $player){
		$eid = $this->getEid($player);
		$dv = $player->getDirectionVector();
		$uuid = UUID::fromRandom();

		$x = $player->getX() + ($dv->getX() * 3);
		$y = $player->getY();
		$z = $player->getZ() + ($dv->getZ() * 3);
		$yaw = $player->yaw - 180;

		$this->sparring[$player->getName()]["x"] = $x;
		$this->sparring[$player->getName()]["y"] = $y;
		$this->sparring[$player->getName()]["z"] = $z;
		$this->sparring[$player->getName()]["yaw"] = $yaw;

		$pk = new AddPlayerPacket();
		$pk->uuid = $uuid;
		$pk->username = "Sparring Target";
		$pk->entityRuntimeId = $eid;
		$pk->x = $x;
		$pk->y = $y;
		$pk->z = $z;
		$pk->pitch = 0;
		$pk->headYaw = $player->yaw - 180;
		$pk->yaw = $player->yaw - 180;
		$pk->item = Item::get(0);
		$flags = 0;
		$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, TextFormat::AQUA.$player->getName()."'s Sparring Target\nHits: 0\nDamage: 0"],
			Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
		];

		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->getLevel()->getName() == "KitSpawn"){
				$player->dataPacket($pk);
			}
		}
	}

	public function despawnSparringTarget(Player $player, $eid){
		$pk = new RemoveEntityPacket();
		$pk->entityUniqueId = $eid;
		foreach($this->plugin->getServer()->getOnlinePlayers() as $p){
			$p->dataPacket($pk);
		}
		$player->dataPacket($pk);
	}

	public function hitTarget(Player $player, $damage){
		$this->addHit($player);
		$this->addDamage($player, $damage);

		$this->updateSparringTarget($player);
	}

	public function updateSparringTarget(Player $player){
		$hits = $this->getHits($player);
		$damage = $this->getDamage($player);

		$pk = new SetEntityDataPacket();
		$pk->entityRuntimeId = $this->getEid($player);
		$flags = 0;
		$flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
		$flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, TextFormat::AQUA.$player->getName()."'s Sparring Target\nHits: ".$hits."\nDamage: ".$damage],
			Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1],
		];
		foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
			if($player->getLevel()->getName() == "KitSpawn"){
				$player->dataPacket($pk);
			}
		}
	}

	public function getEid(Player $player){
		return $this->sparring[$player->getName()]["eid"];
	}

	public function getHits(Player $player){
		return $this->sparring[$player->getName()]["hits"];
	}

	public function addHit(Player $player){
		$this->sparring[$player->getName()]["hits"] += 1;
	}

	public function getDamage(Player $player){
		return $this->sparring[$player->getName()]["damage"];
	}

	public function addDamage(Player $player, float $value){
		$this->sparring[$player->getName()]["damage"] += $value;
	}

	public function getTime(Player $player){
		return $this->sparring[$player->getName()]["time"];
	}

}