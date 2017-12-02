<?php namespace kitpvp\combat\teams;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;
use kitpvp\combat\teams\commands\Team;

class Teams{

	public $plugin;
	public $combat;

	public $teams = [];
	public $team_req = [];
	public static $teamCount = 0;

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;

		$plugin->getServer()->getCommandMap()->register("team", new Team($plugin, "team", "Team with a player!"));
	}

	public function onQuit(Player $player){
		if($this->inTeam($player)){
			$this->disbandTeam($this->teams->getPlayerTeamUid($player));
		}
		$this->closeTeamRequestsTo($player);
		$this->closeTeamRequestsFrom($player);
	}

	public function inTeam(Player $player){
		foreach($this->teams as $uid => $things){
			if($things["players"][0] == strtolower($player->getName())) return true;
			if($things["players"][1] == strtolower($player->getName())) return true;
		}
		return false;
	}

	public function createTeam(Player $player1, Player $player2){
		$uid = self::$teamCount++;
		$this->teams[$uid] = [
			"players" => [
				strtolower($player1->getName()),
				strtolower($player2->getName())
			],
			"kills" => 0,
			"deaths" => 0,

			"creation" => time(),
		];
		if($this->plugin->getCombat()->getSlay()->isAssistingPlayer($player1, $player2)) unset($this->plugin->getCombat()->getSlay()->assists[$player1->getName()][$player2->getName()]);
		if($this->plugin->getCombat()->getSlay()->isAssistingPlayer($player2, $player1)) unset($this->plugin->getCombat()->getSlay()->assists[$player2->getName()][$player1->getName()]);
	}

	public function getPlayerTeamUid(Player $player){
		if(!$this->inTeam($player)) return false;
		foreach($this->teams as $uid => $things){
			if($things["players"][0] == strtolower($player->getName())) return $uid;
			if($things["players"][1] == strtolower($player->getName())) return $uid;
		}
	}

	public function getTeamArray($uid){
		return $this->teams[$uid] ?? false;
	}

	public function disbandTeam($uid){
		$array = $this->getTeamArray($uid);
		$player1 = $this->plugin->getServer()->getPlayerExact($array["players"][0]);
		$player2 = $this->plugin->getServer()->getPlayerExact($array["players"][1]);

		unset($this->teams[$uid]);
		$player1->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."Your team has been disbanded!");
		$player2->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."Your team has been disbanded!");
	}

	public function disbandTeamByPlayer(Player $player){
		$this->disbandTeam($this->getPlayerTeamUid($player));
	}

	public function getTeamKills($uid){
		return $this->teams[$uid]["kills"];
	}

	public function addTeamKill($uid){
		$this->teams[$uid]["kills"]++;
	}

	public function getTeamDeaths($uid){
		return $this->teams[$uid]["deaths"];
	}

	public function addTeamDeath($uid){
		$this->teams[$uid]["deaths"]++;
	}

	public function getTeamCreationTime($uid){
		return $this->teams[$uid]["creation"];
	}

	public function sendTeamRequest(Player $sender, Player $receiver){
		$this->team_req[$sender->getName()][] = [$receiver->getName(),time()];
		$receiver->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."You received a team request from ".$sender->getName()."! Accept it using /team accept ".$sender->getName());
	}

	public function hasTeamRequestFrom(Player $player, Player $from){
		if(!isset($this->team_req[$from->getName()])) return false;
		foreach($this->team_req[$from->getName()] as $index => $data){
			if($data[0] = $player->getName()) return true;
		}
		return false;
	}

	public function closeTeamRequestsTo(Player $player){
		foreach($this->team_req as $index => $array){
			foreach($array as $data){
				if($data[0] = $player->getName()) unset($this->team_req[$index]);
			}
		}
	}

	public function closeTeamRequestsFrom(Player $player){
		unset($this->team_req[$player->getName()]);
	}

	public function cancelTeamRequest(Player $sender, Player $receiver){

	}

	public function acceptTeamRequest(Player $sender, Player $receiver){
		$this->closeTeamRequestsFrom($sender);
		$this->closeTeamRequestsFrom($receiver);
		$this->closeTeamRequestsTo($sender);
		$this->closeTeamRequestsTo($receiver);

		$this->createTeam($sender, $receiver);

		$sender->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."You have teamed with ".$receiver->getName()."!");
		$receiver->sendMessage(TextFormat::AQUA."Teams> ".TextFormat::GREEN."You have teamed with ".$sender->getName()."!");
	}

}