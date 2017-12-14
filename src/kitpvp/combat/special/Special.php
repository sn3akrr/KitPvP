<?php namespace kitpvp\combat\special;

use pocketmine\item\Item;
use pocketmine\entity\{
	Entity,
	Effect
};
use pocketmine\network\mcpe\protocol\LevelEventPacket;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;
use kitpvp\combat\special\other\Spell;

use kitpvp\combat\special\items\{
	SpecialWeapon,

	FryingPan,
	BookOfSpells,
	ConcussionGrenade,
	BrassKnuckles,
	Gun,
	ReflexHammer,
	Defibrillator,
	Syringe,
	Kunai,
	SpikedClub,
	EnderPearl,
	Decoy,
	Flamethrower,
	FireAxe,
	MaloneSword
};
use kitpvp\combat\special\entities\{
	ThrownConcussionGrenade,
	Bullet,
	ThrownKunai,
	ThrownEnderpearl,
	ThrownDecoy,
	Flame
};

use pocketmine\Player;

class Special{

	public $plugin;
	public $combat;

	public $spells = [];
	public $tickers = [];

	public $bleeding = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;

		$this->registerSpells();
		$this->registerTickers();

		$plugin->getServer()->getPluginManager()->registerEvents(new EventListener($plugin, $this), $plugin);
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new SpecialTask($plugin), 10);

		Entity::registerEntity(ThrownConcussionGrenade::class);
		Entity::registerEntity(Bullet::class);
		Entity::registerEntity(ThrownKunai::class);
		Entity::registerEntity(ThrownEnderpearl::class);
		Entity::registerEntity(ThrownDecoy::class);
		Entity::registerEntity(Flame::class);

		foreach($this->plugin->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if(
					$entity instanceof Bullet || 
					$entity instanceof ThrownKunai ||
					$entity instanceof ThrownEnderpearl ||
					$entity instanceof ThrownDecoy ||
					$entity instanceof Flame
				) $entity->close();
			}
		}
	}

	public function tick(){
		foreach($this->getTickers() as $ticker){
			$ticker->tick();
		}
	}

	public function registerSpells(){
		foreach([
			"Spell of Flames" => "burn",
			"Spell of Weight" => Effect::getEffect(Effect::SLOWNESS),
			"Spell of Illness" => Effect::getEffect(Effect::NAUSEA),
			"Spell of Toxins" => Effect::getEffect(Effect::POISON),
			"Spell of Exhaustion" => Effect::getEffect(Effect::MINING_FATIGUE),
			"Spell of Darkness" => Effect::getEffect(Effect::BLINDNESS),
			"Spell of Decay" => Effect::getEffect(Effect::WITHER),
			"Spell of Deficiency" => Effect::getEffect(Effect::WEAKNESS)
		] as $name => $spell) $this->spells[] = new Spell($name, $spell);
	}

	public function registerTickers(){
		foreach([
			new SpecialTicker("fryingpan", "Frying Pan", new FryingPan()),
			new SpecialTicker("bookofspells", "Book of Spells", new BookOfSpells(), 15),
			new SpecialTicker("concussiongrenade", "Concussion Grenade", new ConcussionGrenade(), 5),
			new SpecialTicker("brassknuckles", "Brass Knuckles", new BrassKnuckles()),
			new SpecialTicker("gun", "Gun", new Gun(), 3),
			new SpecialTicker("reflexhammer", "Reflex Hammer", new ReflexHammer()),
			new SpecialTicker("defibrillator", "Defibrillator", new Defibrillator(), 10),
			new SpecialTicker("syringe", "Syringe", new Syringe(), 5),
			new SpecialTicker("kunai", "Kunai", new Kunai(), 2),
			new SpecialTicker("spikedclub", "Spiked Club", new SpikedClub()),
			new SpecialTicker("enderpearl", "Ender Pearl", new EnderPearl(), 15),
			new SpecialTicker("decoy", "Decoy", new Decoy(), 10, 3),
			new SpecialTicker("flamethrower", "Flamethrower", new Flamethrower(), 3),
			new SpecialTicker("fireaxe", "Fire Axe", new FireAxe()),
			new SpecialTicker("malonesword", "M4L0NESWORD", new MaloneSword()),
		] as $special) $this->tickers[] = $special;
	}

	public function getSpells(){
		return $this->spells;
	}

	public function getRandomSpell(){
		return $this->spells[mt_rand(0, count($this->spells) - 1)];
	}

	public function getTickerByItem(Item $item){
		if(!$item instanceof SpecialWeapon) return null;
		foreach($this->getTickers() as $ticker){
			if($ticker->isTrigger($item)) return $ticker;
		}
		return null;
	}

	public function getTickers(){
		return $this->tickers;
	}

	public function cg(Player $player, Player $thrower){
		$teams = $this->combat->getTeams();
		if($teams->sameTeam($player, $thrower)){
			return;
		}
		$this->plugin->getCombat()->getSlay()->damageAs($thrower, $player, 5);

		$pk = new LevelEventPacket();
		$pk->evid = 3501;
		$pk->position = $player->asVector3();
		$pk->data = 0;
		foreach($player->getLevel()->getPlayers() as $p) $p->dataPacket($pk);

		$player->addEffect(Effect::getEffect(Effect::SLOWNESS)->setDuration(20 * 8)->setAmplifier(3));
		$player->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 8));
	}

	public function bleed(Player $player, Player $killer, $seconds){
		$this->bleeding[$player->getName()] = [
			"time" => time() + $seconds,
			"attacker" => $killer
		];
	}

	public function isBleeding(Player $player){
		return isset($this->bleeding[$player->getName()]) && $this->bleeding[$player->getName()]["time"] > time() && $this->bleeding[$player->getName()]["attacker"] instanceof Player;
	}

}