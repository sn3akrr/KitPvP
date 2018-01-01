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

use kitpvp\kits\abilities\{
	components\AbilityTicker,
	components\AbilityListener,
	components\AbilityTask,

	Ability,

	Curse,
	StealthMode,
	LastChance,
	DoubleJump,
	Bounceback,
	Adrenaline,
	LifeSteal,
	Miracle,
	Recover,
	AimAssist,
	Slender,
	ArrowDodge,
	FireAura,
	HealthBoost
};
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

	public $abilities = [];
	public $tickers = [];

	public $kits = [];
	public $invisible = [];

	public $sessions = [];

	public function __construct(KitPvP $plugin){
		$this->plugin = $plugin;
		$this->database = $plugin->database;

		foreach([

		] as $query) $this->database->query($query);

		foreach([
			"kit" => new Kit($plugin, "kit", "Equip a kit!"),
		] as $name => $class) $this->plugin->getServer()->getCommandMap()->register($name, $class);

		$this->registerAbilities();
		$this->registerKits();

		$plugin->getServer()->getPluginManager()->registerEvents(new AbilityListener($plugin, $this), $plugin);
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new AbilityTask($plugin), 1);
	}

	public function close(){
		foreach($this->sessions as $name => $session){
			if($session->hasKit()){
				$session->getKit()->refund($session->getPlayer());
			}
			$session->close();
		}
	}

	public function registerAbilities(){
		$this->abilities = [
			new Curse(),
			new StealthMode(),
			new LastChance(),
			new DoubleJump(),
			new Bounceback(),
			new Adrenaline(),
			new LifeSteal(),
			new Miracle(),
			new Recover(),
			new AimAssist(),
			new Slender(),
			new ArrowDodge(),
			new FireAura(),
			new HealthBoost(),
		];
		$this->registerAbilityTickers();
	}

	public function registerAbilityTickers(){
		foreach($this->getAbilities() as $ability){
			if($ability->doesTick()){
				$this->tickers[$ability->getName()] = new AbilityTicker($ability->getName(), $ability->getTickRate());
			}
		}
	}

	public function getAbilityTickers(){
		return $this->tickers;
	}

	public function getAbilities(){
		return $this->abilities;
	}

	public function getAbility($name){
		foreach($this->getAbilities() as $ability){
			if($ability->getName() == $name) return clone $ability;
		}
		return null;
	}

	public function getCorrespondingTicker(Ability $ability){
		foreach($this->getAbilityTickers() as $ticker){
			if($ticker->getName() == $ability->getName()) return $ticker;
		}
		return null;
	}

	public function registerKits(){
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
				$this->getAbility("curse"),
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
				$this->getAbility("stealth mode"),
				$this->getAbility("last chance"),
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
				$this->getAbility("double jump"),
				$this->getAbility("bounceback"),
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
				$this->getAbility("adrenaline"),
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
				$this->getAbility("life steal"),
				$this->getAbility("miracle"),
				$this->getAbility("recover"),
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
				$this->getAbility("aim assist"),
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
				$this->getAbility("slender"),
				$this->getAbility("arrow dodge"),
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
				$this->getAbility("fire aura"),
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
				$this->getAbility("health boost"),
				$this->getAbility("bounceback"),
			], [
				new MaloneSword(),
			], 3),

		] as $name => $class) $this->kits[$name] = $class;
	}

	public function kitExists($name){
		return isset($this->kits[$name]);
	}

	public function getBaseKit($name){
		return $this->kits[$name] ?? new KitObject("invalid", "default", 0, [], [], [], []);
	}

	public function getKit($name){
		return clone $this->kits[$name] ?? new KitObject("invalid", "default", 0, [], [], [], []);
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
		return $this->invisible[$player->getName()] ?? false;
	}

	public function setInvisible(Player $player, $bool){
		$this->invisible[$player->getName()] = (bool) $bool;
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