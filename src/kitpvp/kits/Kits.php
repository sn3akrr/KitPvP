<?php namespace kitpvp\kits;

use pocketmine\item\Item;
use pocketmine\entity\{
	Entity,
	Effect
};
use pocketmine\utils\{
	TextFormat,
	Color
};
use pocketmine\network\mcpe\protocol\{
	MobEquipmentPacket,
	MobArmorEquipmentPacket
};
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\kits\commands\{
	Kit
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

	public $sessions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([

		] as $query) $this->database->query($query);

		foreach([
			"kit" => new Kit($plugin, "kit", "Equip a kit!"),
		] as $name => $class) $this->plugin->getServer()->getCommandMap()->register($name, $class);

		foreach([
			"noob" => new KitObject("noob", "default", 0, [
				Item::get(272),
				Item::get(366,0,4),
				Item::get(260,0,4),
			], [
				Item::get(314),
				Item::get(303),
				Item::get(0),
				Item::get(317)
			],[],[],[
				new FryingPan(),
			]),

			"witch" => new KitObject("witch", "default", 10, [
				Item::get(272),
				Item::get(322),
				Item::get(366,0,6),
			], [
				Item::get(298),
				Item::get(299),
				Item::get(304),
				Item::get(301)
			], [
				Effect::getEffect(Effect::FIRE_RESISTANCE)
			], [
				"Curse" => "5% chance of attackers being poisoned"
			], [
				new BookOfSpells(),
			]),

			"spy" => new KitObject("spy", "default", 20, [
				Item::get(267),
				Item::get(364,0,2),
				Item::get(320,0,4),
			], [
				Item::get(298),
				Item::get(307),
				Item::get(0),
				Item::get(309)
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
				Item::get(267),
				Item::get(320,0,4),
				Item::get(364,0,2),
			], [
				Item::get(306),
				Item::get(299),
				Item::get(0),
				Item::get(313)
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
				Item::get(267),
				Item::get(364,0,2),
				Item::get(424,0,4),
			], [
				Item::get(310),
				Item::get(307),
				Item::get(300),
				Item::get(0),
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(1),
				Effect::getEffect(Effect::DAMAGE_RESISTANCE)
			], [
				"Adrenaline" => "Increased jump and speed boost when low on health"
			], [
				new Gun()
			], 1),

			"medic" => new KitObject("medic", "blaze", 10, [
				Item::get(267),
				Item::get(260,0,16),
			], [
				Item::get(0),
				Item::get(315),
				Item::get(300),
				Item::get(313)
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
				Item::get(261),
				Item::get(262,0,64),
				Item::get(260,0,16),
				Item::get(320,0,4),
			], [
				Item::get(302),
				Item::get(299),
				Item::get(308),
				Item::get(301)
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
				Item::get(267),
				Item::get(364,0,8),
			], [
				Item::get(302),
				Item::get(299),
				Item::get(312),
				Item::get(0),
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(1),
				Effect::getEffect(Effect::MINING_FATIGUE)->setAmplifier(1),
				Effect::getEffect(Effect::STRENGTH),
			], [
				"Slender" => "All enemies nearby are blinded when you're low on health, one time use",
				"Arrow Dodge" => "25% chance of arrow attacks to be dodged"
			], [
				new EnderPearl(0, 4),
				new Decoy(0, 3),
			], 2),

			"pyromancer" => new KitObject("pyromancer", "wither", 40, [
				Item::get(364,0,8),
			], [
				Item::get(0),
				Item::get(307),
				Item::get(300),
				Item::get(309)
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
				Item::get(322),
			], [
				Item::get(310),
				Item::get(311),
				Item::get(0),
				Item::get(0)
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

	public function close(){
		foreach($this->sessions as $name => $session){
			$session->save();
		}
	}

	public function kitExists($name){
		return isset($this->kits[$name]);
	}

	public function getKit($name){
		return $this->kits[$name] ?? new KitObject("invalid", "default", 0, [], [], [], []);
	}

	public function getKitNum($name){
		$key = 1;
		foreach($this->kits as $n => $kit){
			if($kit->getName() == $name) return $key;
			$key++;
		}
		return -1;
	}

	public function getKitList(){
		$list = [];
		foreach($this->kits as $kit){
			$list[] = $kit->getName();
		}
		return $list;
	}

	public function createSession(Player $player){
		return $this->sessions[$player->getName()] = new Session($player);
	}

	public function getSession(Player $player){
		return $this->sessions[$player->getName()] ?? $this->createSession($player);
	}

	public function deleteSession(Player $player){
		$this->sessions[$player->getName()]->save();
		unset($this->sessions[$player->getName()]);
	}

	// special stuffs \\
	public function isInvisible(Player $player){
		return $this->getSession($player)->ability["invisible"] ?? false;
	}

	public function setInvisible(Player $player, $bool){
		$session = $this->getSession($player);
		$session->ability["invisible"] = (bool) $bool;
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