<?php namespace kitpvp\combat\teams;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

class Team{

	public $id;
	public $createdtime;

	public $member1;
	public $member2;

	public $kills;
	public $deaths;

	public $closed = false;

	public function __construct(Player $member1, Player $member2){
		$this->member1 = $member1;
		$this->member2 = $member2;

		$this->id = Teams::$teamCount++;
		$this->createdtime = time();

		$this->kills = 0;
		$this->deaths = 0;

		$member1->sendMessage(TextFormat::GREEN . "You are now teamed with " . $member2->getName());
		$member2->sendMessage(TextFormat::GREEN . "You are now teamed with " . $member1->getName());
	}

	public function inTeam(Player $player){
		return $player == $this->getMember1() || $player == $this->getMember2();
	}

	public function getOppositeMember(Player $player){
		if($player == $this->getMember1()) return $this->getMember2();
		return $this->getMember1();
	}

	public function getId(){
		return $this->id;
	}

	public function getCreatedTime(){
		return $this->createdtime;
	}

	public function getMember1(){
		return $this->member1;
	}

	public function getMember2(){
		return $this->member2;
	}

	public function getKills(){
		return $this->kills;
	}

	public function addKill(){
		$this->kills++;
	}

	public function getDeaths(){
		return $this->deaths;
	}

	public function addDeath(){
		$this->deaths++;
	}

	public function getKdr(){
		if($this->getDeaths() == 0) return "N/A";
		return round($this->getKills() / $this->getDeaths(), 2);
	}

	public function isClosed(){
		return $this->closed;
	}

	final public function disband($reason = "Unknown"){
		$this->closed = true;
		$this->getMember1()->sendMessage(TextFormat::RED."Your team has been disbanded! (".$reason.")");
		$this->getMember2()->sendMessage(TextFormat::RED."Your team has been disbanded! (".$reason.")");

		unset(KitPvP::getInstance()->getCombat()->getTeams()->teams[$this->getId()]);
	}

}