<?php namespace kitpvp\kits;

use pocketmine\item\Item;
use pocketmine\entity\{
	Entity,
	Effect
};
use pocketmine\utils\TextFormat;
use pocketmine\network\mcpe\protocol\{
	MobEquipmentPacket,
	MobArmorEquipmentPacket
};
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\kits\commands\{
	Kit,
	ReplenishKit,
	KitPass,
	AddKitPasses
};
use kitpvp\kits\components\{
	KitPowerListener,
	KitPowerTask
};
use kitpvp\combat\special\other\Spell;
use kitpvp\kits\event\KitUnequipEvent;
use kitpvp\combat\special\SpecialIds as SID;

use kitpvp\combat\special\items\{
	FryingPan,
	BookOfSpells,
	ConcussionGrenade,
	BrassKnuckles,
	Gun,
	ReflexHammer,
	Defibrillator,
	Syringe,
	SpikedClub,
	Kunai,
	EnderPearl,
	Decoy,
	FireAxe,
	Flamethrower,
	MaloneSword
};

use core\stats\User;

class Kits{

	public $plugin;
	public $database;

	public $kits;

	public $confirm = [];
	public $equipped = [];

	public $ability = [];

	public $kp = [];
	public $uic = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([
			"CREATE TABLE IF NOT EXISTS kits_kitpasses(xuid BIGINT(16) NOT NULL UNIQUE, passes INT NOT NULL, cooldown INT NOT NULL)",
		] as $query) $this->database->query($query);

		foreach([
			"kit" => new Kit($plugin, "kit", "Equip a kit!"),
			"replenishkit" => new ReplenishKit($plugin, "replenishkit", "Replenish your kit items!"),
			"kitpass" => new KitPass($plugin, "kitpass", "Toggle kit pass"),
			"addkitpasses" => new AddKitPasses($plugin, "addkitpasses", "Give player's kit passes"),
		] as $name => $class) $this->plugin->getServer()->getCommandMap()->register($name, $class);

		foreach([
			"noob" => new KitObject("noob", "default", 0, [
				Item::get(272,0,1),
				Item::get(366,0,4),
				Item::get(260,0,4),

				Item::get(314,0,1),
				Item::get(303,0,1),
				Item::get(317,0,1)
			],[],[],[
				new FryingPan(),
			]),

			"witch" => new KitObject("witch", "default", 10, [
				Item::get(272,0,1),
				Item::get(322,0,1),
				Item::get(366,0,6),

				Item::get(298,0,1),
				Item::get(299,0,1),
				Item::get(304,0,1),
				Item::get(301,0,1)
			], [
				Effect::getEffect(Effect::FIRE_RESISTANCE)
			], [
				"Curse" => "5% chance of attackers being poisoned"
			], [
				new BookOfSpells(),
			]),

			"spy" => new KitObject("spy", "default", 20, [
				Item::get(267,0,1),
				Item::get(364,0,2),
				Item::get(320,0,4),

				Item::get(298,0,1),
				Item::get(307,0,1),
				Item::get(309,0,1)
			], [
				Effect::getEffect(Effect::HASTE),
				Effect::getEffect(Effect::SPEED),
			], [
				"Stealth Mode" => "Invisibility when holding still or sneaking",
				"Last Chance" => "Knocks back players and 5 second invisibility when low on health"
			], [
				new ConcussionGrenade(0, 3)
			]),

			"scout" => new KitObject("scout", "default", 30, [
				Item::get(267,0,1),
				Item::get(320,0,4),
				Item::get(364,0,2),

				Item::get(306,0,1),
				Item::get(299,0,1),
				Item::get(313,0,1)
			], [
				Effect::getEffect(Effect::HASTE),
				Effect::getEffect(Effect::SPEED)->setAmplifier(3),
			], [
				"Double Jump" => "Self explanitory",
				"Bounceback" => "25% chance players attacking you will get recoil knockback"
			], [
				new BrassKnuckles()
			]),

			"assault" => new KitObject("assault", "default", 40, [
				Item::get(267,0,1),
				Item::get(364,0,2),
				Item::get(424,0,4),

				Item::get(310,0,1),
				Item::get(307,0,1),
				Item::get(300,0,1)
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(1),
				Effect::getEffect(Effect::DAMAGE_RESISTANCE)
			], [
				"Adrenaline" => "Increased jump and speed boost when low on health"
			], [
				new Gun()
			], 1),

			"medic" => new KitObject("medic", "blaze", 10, [
				Item::get(267,0,1),
				Item::get(260,0,16),

				Item::get(315,0,1),
				Item::get(300,0,1),
				Item::get(313,0,1)
			], [], [
				"Life Steal" => "Gain health when killing players",
				"Miracle" => "Regains 2.5 hearts when low on health one time",
				"Recover" => "Slowly regenerate health over time"
			], [
				new ReflexHammer(),
				new Defibrillator(),
				new Syringe(),
			], 1),

			"archer" => new KitObject("archer", "ghast", 20, [
				Item::get(261,0,1),
				Item::get(262,0,64),
				//Item::get(267,0,1),
				Item::get(260,0,16),
				Item::get(320,0,4),

				Item::get(302,0,1),
				Item::get(299,0,1),
				Item::get(308,0,1),
				Item::get(301,0,1)
			], [
				Effect::getEffect(Effect::SPEED),
				Effect::getEffect(Effect::JUMP)
			], [
				"Aim Assist" => "Bow automatically aims on nearby target"
			], [
				new SpikedClub(),
				new Kunai(0, 3),
			], 1),

			"enderman" => new KitObject("enderman", "enderman", 30, [
				Item::get(267,0,1), //REPLACE -- STICK w/ KNOCKBACK
				Item::get(364,0,8),

				Item::get(302,0,1),
				Item::get(299,0,1),
				Item::get(312,0,1)
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(1),
				Effect::getEffect(Effect::STRENGTH)
			], [
				"Slender" => "All enemies nearby are blinded when you're low on health, one time use",
				"Arrow Dodge" => "25% chance of arrow attacks to be dodged"
			], [
				new EnderPearl(0, 16),
				new Decoy(0, 8),
			], 1),

			"pyromancer" => new KitObject("pyromancer", "wither", 40, [
				Item::get(364,0,8),

				Item::get(307,0,1),
				Item::get(300,0,1),
				Item::get(309,0,1)
			], [
				Effect::getEffect(Effect::SLOWNESS),
				Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setAmplifier(1),
				Effect::getEffect(Effect::FIRE_RESISTANCE)
			], [
				"Fire Aura" => "Enemies nearby are slowly damaged when nearby"
			], [
				new FireAxe(),
				new FlameThrower(),
			], 2),

			"m4l0ne23" => new KitObject("m4l0ne23", "enderdragon", 50, [
				Item::get(364,0,16),
				Item::get(322,0,1),

				Item::get(310,0,1),
				Item::get(311,0,1)
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(2),
				Effect::getEffect(Effect::MINING_FATIGUE)->setAmplifier(1)
			], [
				"Health Boost" => "2 extra hearts",
				"Bounceback" => "25% chance players attacking you will get recoil knockback"
			], [
				new MaloneSword(),
			], 3),

		] as $name => $class) $this->kits[$name] = $class;

		$plugin->getServer()->getPluginManager()->registerEvents(new KitPowerListener($plugin, $this), $plugin);
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new KitPowerTask($plugin), 5);
	}

	public function kitExists($name){
		return isset($this->kits[$name]);
	}

	public function hasKit(Player $player){
		return isset($this->equipped[strtolower($player->getName())]);
	}

	public function getKit($name){
		return $this->kits[$name] ?? new KitObject("invalid", "default", 0, [], [], [], []);
	}

	public function getPlayerKit(Player $player){
		if(!isset($this->equipped[strtolower($player->getName())])) return new KitObject("invalid", "default", 0, [], [], [], []);
		return $this->kits[$this->equipped[strtolower($player->getName())]];
	}

	public function setEquipped(Player $player, $equipped = true, $kitname = null){
		if($equipped){
			$this->equipped[$player->getName()] = $kitname;
		}else{
			$this->plugin->getServer()->getPluginManager()->callEvent(new KitUnequipEvent($player));
			unset($this->equipped[$player->getName()]);
		}
	}

	public function getKitList(){
		$list = [];
		foreach($this->kits as $kit){
			$list[] = $kit->getName();
		}
		return $list;
	}

	//Kit passes
	public function getKitPasses($player){
		$xuid = (new User($player))->getXuid();

		$statement = $this->database->prepare("SELECT passes FROM kits_kitpasses WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($passes);
		if($statement->execute()){
			$statement->fetch();
		}
		$statement->close();

		return $passes ?? 0;
	}

	public function setKitPasses($player, $amount){
		$xuid = (new User($player))->getXuid();

		$z = 0;
		$statement = $this->database->prepare("INSERT INTO kits_kitpasses(xuid, passes, cooldown) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE passes=VALUES(passes)");
		$statement->bind_param("iii", $xuid, $amount, $z);
		$statement->execute();
		$statement->close();
	}

	public function addKitPasses($player,  $amount){
		$amount = $this->getKitPasses($player) + $amount;
		$this->setKitPasses($player, $amount);
	}

	public function takeKitPasses($player, $amount){
		$amount = $this->getKitPasses($player) - $amount;
		$this->setKitPasses($player, $amount);
	}

	public function setPassCooldown($player){
		$xuid = (new User($player))->getXuid();
		$f = 5;

		$statement = $this->database->prepare("UPDATE kits_kitpasses SET cooldown=? WHERE xuid=?");
		$statement->bind_param("ii", $f, $xuid);
		$statement->execute();
		$statement->close();
	}

	public function subtractPassCooldown($player){
		$xuid = (new User($player))->getXuid();

		$statement = $this->database->prepare("UPDATE kits_kitpasses SET cooldown=cooldown-1 WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->execute();
		$statement->close();
	}

	public function hasPassCooldown($player){
		$xuid = (new User($player))->getXuid();
		$statement = $this->database->prepare("SELECT cooldown FROM kits_kitpasses WHERE xuid=?");
		$statement->bind_param("i", $xuid);
		$statement->bind_result($cooldown);
		if($statement->execute()){
			$statement->fetch();
		}
		$statement->close();

		return ($cooldown ?? 0) > 0;
	}

	public function toggleKitPass(Player $player){
		if(!isset($this->kp[$player->getName()])){
			$this->kp[$player->getName()] = true;
			return true;
		}else{
			unset($this->kp[$player->getName()]);
			return false;
		}
	}

	public function hasKitPassActive(Player $player){
		return isset($this->kp[$player->getName()]);
	}

	public function consumeKitPass(Player $player){
		unset($this->kp[$player->getName()]);
		$this->takeKitPasses($player, 1);
		$this->setPassCooldown($player);

		$player->sendMessage(TextFormat::LIGHT_PURPLE."You used one kit pass! The kit you equipped was free of charge!");
	}

	// special stuffs \\
	public function isInvisible(Player $player){
		return $this->ability[$player->getName()]["invisible"] ?? false;
	}

	public function setInvisible(Player $player, $bool){
		$this->ability[$player->getName()]["invisible"] = $bool;
		switch($bool){
			case true:
				$player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
				foreach($player->getLevel()->getPlayers() as $p){
					$pk = new MobArmorEquipmentPacket();
					$pk->entityRuntimeId = $player->getId();
					$pk->slots = [Item::get(0),Item::get(0),Item::get(0),Item::get(0)];
					$p->dataPacket($pk);

					if($p != $player){
						$pk = new MobEquipmentPacket();
						$pk->entityRuntimeId = $player->getId();
						$pk->item = $player->getInventory()->getItemInHand();
						$pk->inventorySlot = $pk->hotbarSlot = $player->getInventory()->getHeldItemIndex();
						$p->dataPacket($pk);
					}
				}
			break;
			case false:
				$player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
				foreach($player->getLevel()->getPlayers() as $p){
					$pk = new MobArmorEquipmentPacket();
					$pk->entityRuntimeId = $player->getId();
					$pk->slots = $player->getInventory()->getArmorContents();
					$p->dataPacket($pk);

					if($p != $player){
						$pk = new MobEquipmentPacket();
						$pk->entityRuntimeId = $player->getId();
						$pk->item = Item::get(0);
						$pk->inventorySlot = $pk->hotbarSlot = 1;
						$p->dataPacket($pk);
					}
				}
			break;
		}
	}

}