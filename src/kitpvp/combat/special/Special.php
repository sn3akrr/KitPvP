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
	ThrowingKnife,
	Shuriken,
	EnderPearl,
	Decoy,
	Flamethrower,
	MaloneSword
};
use kitpvp\combat\special\entities\{
	ThrownConcussionGrenade,
	Bullet,
	ThrownEnderpearl,
	ThrownDecoy,
	Flame
};

use core\AtPlayer as Player;

class Special implements SpecialIds{

	const TYPE_INTERACT = 0;
	const TYPE_ATTACK = 1;

	public $plugin;
	public $combat;

	public $spells = [];

	//Special weapon runs
	public $special = [];

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;

		$this->registerWeapons();
		$this->registerSpells();
		$plugin->getServer()->getPluginManager()->registerEvents(new EventListener($plugin, $this), $plugin);
		$plugin->getServer()->getScheduler()->scheduleRepeatingTask(new SpecialTask($plugin), 10);

		Entity::registerEntity(ThrownConcussionGrenade::class);
		Entity::registerEntity(Bullet::class);
		Entity::registerEntity(ThrownEnderpearl::class);
		Entity::registerEntity(ThrownDecoy::class);
		Entity::registerEntity(Flame::class);

		foreach($this->plugin->getServer()->getLevels() as $level){
			foreach($level->getEntities() as $entity){
				if(
					$entity instanceof Bullet || 
					$entity instanceof ThrownEnderpearl ||
					$entity instanceof ThrownDecoy
				) $entity->close();
			}
		}  
	}

	public function registerWeapons(){
		Item::$list[self::BOOK_OF_SPELLS] = [0 => new BookOfSpells()];
		Item::$list[self::CONCUSSION_GRENADE] = [0 => new ConcussionGrenade()];
		Item::$list[self::BRASS_KNUCKLES] = [0 => new BrassKnuckles()];
		Item::$list[self::GUN] = [0 => new Gun()];
		Item::$list[self::REFLEX_HAMMER] = [0 => new ReflexHammer()];
		Item::$list[self::DEFIBRILLATOR] = [0 => new Defibrillator()];
		Item::$list[self::SYRINGE] = [0 => new Syringe()];
		Item::$list[self::THROWING_KNIFE] = [0 => new ThrowingKnife()];
		Item::$list[self::SHURIKEN] = [0 => new Shuriken()];
		Item::$list[self::ENDER_PEARL] = [0 => new EnderPearl()];
		Item::$list[self::DECOY] = [0 => new Decoy()];
		Item::$list[self::FLAMETHROWER] = [0 => new Flamethrower()];
		Item::$list[self::MALONE_SWORD] = [0 => new MaloneSword()];
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

	public function cg(Player $player, Player $thrower){
		$teams = $this->combat->getTeams();
		if($teams->inTeam($player) && $teams->inTeam($thrower)){
			if($teams->getPlayerTeamUid($player) == $teams->getPlayerTeamUid($thrower)){
				return;
			}
		}
		$this->plugin->getCombat()->getSlay()->damageAs($thrower, $player, 5);

		$pk = new LevelEventPacket();
		$pk->evid = 3501;
		$pk->x = $player->x;
		$pk->y = $player->y;
		$pk->z = $player->z;
		$pk->data = 0;
		foreach($player->getLevel()->getPlayers() as $p) $p->dataPacket($pk);

		$player->addEffect(Effect::getEffect(Effect::SLOWNESS)->setDuration(20 * 8)->setAmplifier(3));
		$player->addEffect(Effect::getEffect(Effect::BLINDNESS)->setDuration(20 * 8));
	}

}