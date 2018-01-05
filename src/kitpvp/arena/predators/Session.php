<?php namespace kitpvp\arena\predators;

use pocketmine\Server;

use kitpvp\KitPvP;

use core\stats\User;

class Session{

	public $user;

	public $kills = [];

	public $normaldeaths = 0;
	public $bossdeaths = 0;

	public function __construct($user){
		$this->user = new User($user);

		$this->load();
	}

	public function load(){
		foreach(KitPvP::getInstance()->getArena()->getPredators()->getPredatorTypes() as $type){
			$this->kills[$type] = 0;
		}

		$xuid = $this->getXuid();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("SELECT knight, pawn, king, robot, cyborg, powermech, jungleman, caveman, gorilla, bandit, cowboy, sheriff, normaldeaths, bossdeaths FROM predators_stats WHERE xuid=?");
		$stmt->bind_param("i", $xuid);
		$stmt->bind_values($knight, $pawn, $king, $robot, $cyborg, $powermech, $jungleman, $caveman, $gorilla, $bandit, $cowboy, $sheriff, $normaldeaths, $bossdeaths);
		if($stmt->execute()){
			$stmt->fetch();
		}
		$stmt->close();

		if($knight == null) return; //New stats

		$this->kills = [
			"knight" => $knight,
			"pawn" => $pawn,
			"king" => $king,

			"robot" => $robot,
			"cyborg" => $cyborg,
			"powermech" => $powermech,

			"jungleman" => $jungleman,
			"caveman" => $caveman,
			"gorilla" => $gorilla,

			"bandit" => $bandit,
			"cowboy" => $cowboy,
			"sheriff" => $sheriff
		];

		$this->normaldeaths = $normaldeaths;
		$this->bossdeaths = $bossdeaths;
	}

	public function getUser(){
		return $this->user;
	}

	public function getPlayer(){
		return $this->getUser()->getPlayer();
	}

	public function getXuid(){
		return $this->getUser()->getXuid();
	}

	public function getKills($type){
		return $this->kills[$type];
	}

	public function getBossKills(){
		$kills = 0;
		foreach(["king", "powermech", "gorilla", "sheriff"] as $boss){
			$kills += $this->getKills($boss);
		}
		return $kills;
	}

	public function addKill($type){
		$this->kills[$type] += 1;

		$kills = $this->getKills($type);

		$as = KitPvP::getInstance()->getAchievements()->getSession($this->getPlayer());
		if(in_array($type, ["king", "powermech", "gorilla", "sheriff"])){
			if($kills >= 1) if(!$as->hasAchievement($type . "_1")) $as->get($type . "_1");
			if($kills >= 50) if(!$as->hasAchievement($type . "_2")) $as->get($type . "_2");
			if($kills >= 250) if(!$as->hasAchievement($type . "_3")) $as->get($type . "_3");
		}else{
			if($kills >= 1) if(!$as->hasAchievement($type . "_1")) $as->get($type . "_1");
			if($kills >= 25) if(!$as->hasAchievement($type . "_2")) $as->get($type . "_2");
			if($kills >= 100) if(!$as->hasAchievement($type . "_3")) $as->get($type . "_3");
			if($kills >= 1000) if(!$as->hasAchievement($type . "_4")) $as->get($type . "_4");
			if($kills >= 10000) if(!$as->hasAchievement($type . "_5")) $as->get($type . "_5");
		}
	}

	public function getDeaths(){
		return $this->normaldeaths;
	}

	public function addDeath($type = ""){
		$this->normaldeaths += 1;

		$as = KitPvP::getInstance()->getAchievements()->getSession($this->getPlayer());

		if($type == "") return;
		if(!$as->hasAchievement($type . "_death")) $as->get($type . "_death");
	}

	public function getBossDeaths(){
		return $this->bossdeaths;
	}

	public function addBossDeath($type = ""){
		$this->bossdeaths += 1;

		$as = KitPvP::getInstance()->getAchievements()->getSession($this->getPlayer());

		if($type == "") return;
		if(!$as->hasAchievement($type . "_death")) $as->get($type . "_death");
	}

	public function save(){
		$xuid = $this->getXuid();
		$knight = $this->getKills("knight");
		$pawn = $this->getKills("pawn");
		$king = $this->getKills("king");
		$robot = $this->getKills("robot");
		$cyborg = $this->getKills("robot");
		$powermech = $this->getKills("powermech");
		$caveman = $this->getKills("caveman");
		$jungleman = $this->getKills("jungleman");
		$gorilla = $this->getKills("gorilla");
		$bandit = $this->getKills("bandit");
		$cowboy = $this->getKills("cowboy");
		$sheriff = $this->getKills("sheriff");

		$normaldeaths = $this->getDeaths();
		$bossdeaths = $this->getBossDeaths();

		$db = KitPvP::getInstance()->database;
		$stmt = $db->prepare("INSERT INTO predators_stats(xuid, knight, pawn, king, robot, cyborg, powermech, jungleman, caveman, gorilla, bandit, cowboy, sheriff, normaldeaths, bossdeaths) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ?) ON DUPLICATE KEY UPDATE knight=VALUES(knight), pawn=VALUES(pawn), king=VALUES(king), robot=VALUES(robot), cyborg=VALUES(cyborg), powermech=VALUES(powermech), jungleman=VALUES(jungleman), caveman=VALUES(caveman), gorilla=VALUES(gorilla), bandit=VALUES(bandit), cowboy=VALUES(cowboy), sheriff=VALUES(sheriff), normaldeaths=VALUES(normaldeath), bossdeaths=VALUES(bossdeaths)");
		$stmt->bind_param("iiiiiiiiiiiiiii", $xuid, $knight, $pawn, $king, $robot, $cyborg, $powermech, $jungleman, $caveman, $gorilla, $bandit, $cowboy, $sheriff, $normaldeaths, $bossdeaths);
		$stmt->execute();
		$stmt->close();
	}

}