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

	BookOfSpells,
	ConcussionGrenade,
	BrassKnuckles,
	Gun,
	ReflexHammer,
	Defibrillator,
	Syringe,
	Kunai,
	EnderPearl,
	Decoy,
	Flamethrower,
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

	const TYPE_INTERACT = 0;
	const TYPE_ATTACK = 1;

	public $plugin;
	public $combat;

	public $spells = [];

	//Special weapon runs
	public $special = [];
	public $bleeding = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;

		$this->registerSpells();
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

	public function getSpells(){
		return $this->spells;
	}

	public function getRandomSpell(){
		return $this->spells[mt_rand(0, count($this->spells) - 1)];
	}

	public function cg(Player $player, Player $thrower){
		$teams = $this->combat->getTeams();
		if($teams->inTeam($player, $thrower)){
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