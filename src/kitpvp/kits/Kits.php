<?php namespace kitpvp\kits;

use pocketmine\item\Item;
use pocketmine\entity\{
	Entity,
	Effect
};
use pocketmine\utils\Color;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;

use kitpvp\KitPvP;
use kitpvp\kits\commands\{
	Kit,
	ReplenishKit
};
use kitpvp\kits\components\{
	KitPowerListener,
	KitPowerTask
};
use kitpvp\combat\special\other\Spell;
use kitpvp\kits\event\KitUnequipEvent;
use kitpvp\combat\special\SpecialIds as SID;

use kitpvp\combat\special\items\{
	BookOfSpells,
	ConcussionGrenade,
	BrassKnuckles,
	Gun,
	ReflexHammer,
	Defibrillator,
	Syringe,
	ThrowingKnife,
	Shuriken,
	EnderPearl,
	Decoy,
	Flamethrower,
	MaloneSword
};

use core\AtPlayer as Player;

class Kits{

	public $plugin;
	public $database;

	public $kits;

	public $confirm = [];
	public $equipped = [];

	//Kit ability data
	public $ability = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([
			"CREATE TABLE IF NOT EXISTS kits_kitpasses(xuid BIGINT(16) NOT NULL UNIQUE, passes INT NOT NULL, cooldown INT NOT NULL)",
		] as $query) $this->database->query($query);

		foreach([
			"kit" => new Kit($plugin, "kit", "Equip a kit!"),
			"replenishkit" => new ReplenishKit($plugin, "replenishkit", "Replenish your kit items!")
		] as $name => $class) $this->plugin->getServer()->getCommandMap()->register($name, $class);

		foreach([
			"noob" => new KitObject("noob", "default", 0, [
				Item::get(272,0,1),
				Item::get(366,0,4),
				Item::get(260,0,4),

				Item::get(298,0,1),
				Item::get(303,0,1),
				Item::get(317,0,1)
			],[]),

			"witch" => new KitObject("witch", "default", 10, [
				//Splash potions, poison, damage, healing
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
				//Item::get(SID::BOOK_OF_SPELLS)
				new BookOfSpells(),
			]),

			"spy" => new KitObject("spy", "default", 20, [
				Item::get(267,0,1), //ADD SHARPNESS AND KNOCKBACK
				//POTION OF INVISIBILITY
				Item::get(364,0,2),
				Item::get(320,0,4),

				Item::get(314,0,1),
				Item::get(307,0,1),
				Item::get(309,0,1)
			], [
				Effect::getEffect(Effect::HASTE),
				Effect::getEffect(Effect::SPEED),
			], [
				"Stealth Mode" => "Invisibility when holding still or sneaking",
				"Last Chance" => "Knocks back players and 5 second invisibility when low on health"
			], [
				//Item::get(SID::CONCUSSION_GRENADE,0,3)
				new ConcussionGrenade(0, 3)
			]),

			"scout" => new KitObject("scout", "default", 30, [
				Item::get(267,0,1), //ADD SHARPNESS AND KNOCKBACK
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
				//Item::get(SID::BRASS_KNUCKLES)
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
				//Item::get(SID::GUN)
				new Gun()
			], 1),

			"medic" => new KitObject("medic", "blaze", 10, [
				//Splash potion of healing, potion of healing II
				Item::get(267,0,1),
				Item::get(260,0,16),

				Item::get(303,0,1),
				Item::get(300,0,1),
				Item::get(313,0,1)
			], [], [
				"Life Steal" => "Gain health when killing players",
				"Miracle" => "Regains 2.5 hearts when low on health one time",
				"Recover" => "Slowly regenerate health over time"
			], [
				//Item::get(SID::REFLEX_HAMMER),
				//Item::get(SID::DEFIBRILLATOR),
				//Item::get(SID::SYRINGE)
				new ReflexHammer(),
				new Defibrillator(),
				new Syringe(),
			], 1),

			"archer" => new KitObject("archer", "ghast", 20, [
				Item::get(261,0,1), //ADD POWER+PUNCH+INFINITY
				Item::get(262,0,64),
				Item::get(272,0,1),
				Item::get(260,0,16),
				Item::get(320,0,4),

				Item::get(298,0,1),
				Item::get(307,0,1),
				Item::get(304,0,1),
				Item::get(309,0,1)
			], [
				Effect::getEffect(Effect::SPEED),
				Effect::getEFfect(Effect::JUMP)
			], [
				"Aim Assist" => "Bow automatically aims on nearby target (TODO)"
			], [
				//Item::get(SID::THROWING_KNIFE),
				//Item::get(SID::SHURIKEN)
				new ThrowingKnife(),
				new Shuriken(),
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
				//Item::get(SID::ENDER_PEARL,0,16),
				//Item::get(SID::DECOY,0,8)
				new EnderPearl(0,16),
				new Decoy(0,8),
			], 1),

			"pyromancer" => new KitObject("pyromancer", "wither", 40, [
				Item::get(267,0,1), //Fire aspect
				Item::get(364,0,8),

				Item::get(307,0,1),
				Item::get(308,0,1),
				Item::get(309,0,1)
			], [
				Effect::getEffect(Effect::SLOWNESS),
				Effect::getEffect(Effect::DAMAGE_RESISTANCE)->setAmplifier(1),
				Effect::getEffect(Effect::FIRE_RESISTANCE)
			], [
				"Fire Aura" => "Enemies nearby are slowly damaged when nearby"
			], [
				//Item::get(SID::FLAMETHROWER)
				new FlameThrower(),
			], 2),

			"m4l0ne23" => new KitObject("m4l0ne23", "enderdragon", 50, [
				Item::get(364,0,16),
				Item::get(322,0,1),

				Item::get(310,0,1),
				Item::get(311,0,1)
			], [
				Effect::getEffect(Effect::SPEED)->setAmplifier(2)
			], [
				"Health Boost" => "2 extra hearts",
				"Bounceback" => "25% chance players attacking you will get recoil knockback"
			], [
				//Item::get(SID::MALONE_SWORD)
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
			$this->equipped[strtolower($player->getName())] = $kitname;
		}else{
			$this->plugin->getServer()->getPluginManager()->callEvent(new KitUnequipEvent($player));
			unset($this->equipped[strtolower($player->getName())]);
		}
	}

	public function getKitList(){
		$list = [];
		foreach($this->kits as $kit){
			$list[] = $kit->getName();
		}
		return $list;
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
				}
			break;
			case false:
				$player->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, false);
				foreach($player->getLevel()->getPlayers() as $p){
					$pk = new MobArmorEquipmentPacket();
					$pk->entityRuntimeId = $player->getId();
					$pk->slots = $player->getInventory()->getArmorContents();
					$p->dataPacket($pk);
				}
			break;
		}
	}

}