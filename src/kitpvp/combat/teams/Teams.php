<?php namespace kitpvp\combat\teams;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

use kitpvp\KitPvP;
use kitpvp\combat\Combat;
use kitpvp\combat\teams\commands\Team as TeamCmd;

class Teams{

	public $plugin;
	public $combat;

	public $teams = [];
	public $requests = [];

	public static $teamCount = 0;
	public static $requestCount = 0;

	public function __construct(KitPvP $plugin, Combat $combat){
		$this->plugin = $plugin;
		$this->combat = $combat;

		$plugin->getServer()->getCommandMap()->register("team", new TeamCmd($plugin, "team", "Team with a player!"));
	}

	public function tick(){
		foreach($this->getRequests() as $request){
			if($request->canTimeout()){
				$request->timeout();
			}
		}
	}

	public function onQuit(Player $player){
		if($this->inTeam($player)){
			$this->getPlayerTeam($player)->disband($player->getName() . " quit the server");
		}
		foreach($this->getRequestsTo($player) as $request){
			$request->timeout();
		}
		foreach($this->getRequestsFrom($player) as $request){
			$request->timeout();
		}
	}

	public function getTeams(){
		return $this->teams;
	}

	public function getTeam($uid){
		return $this->teams[$uid] ?? null;
	}

	public function getPlayerTeam(Player $player){
		foreach($this->getTeams() as $uid => $team){
			if($team->inTeam($player)){
				return $team;
			}
		}
		return null;
	}

	public function inTeam(Player $player){
		return $this->getPlayerTeam($player) != null;
	}

	public function sameTeam(Player $player1, Player $player2){
		return $this->getPlayerTeam($player1) != null &&
		$this->getPlayerTeam($player2) != null &&
		$this->getPlayerTeam($player1) == $this->getPlayerTeam($player2);
	}

	public function createTeam(Player $player1, Player $player2){
		$team = new Team($player1, $player2);
		$this->teams[$team->getId()] = $team;

		foreach($this->getRequestsTo($player1) as $request){
			$request->deny(true, false);
		}
		foreach($this->getRequestsFrom($player1) as $request){
			$request->deny(false);
		}

		foreach($this->getRequestsTo($player2) as $request){
			$request->deny(true, false);
		}
		foreach($this->getRequestsFrom($player2) as $request){
			$request->deny(false);
		}

		if($this->plugin->getCombat()->getSlay()->isAssistingPlayer($player1, $player2)) unset($this->plugin->getCombat()->getSlay()->assists[$player1->getName()][$player2->getName()]);
		if($this->plugin->getCombat()->getSlay()->isAssistingPlayer($player2, $player1)) unset($this->plugin->getCombat()->getSlay()->assists[$player2->getName()][$player1->getName()]);
	}

	public function getRequests(){
		return $this->requests;
	}

	public function getRequest($id){
		return $this->requests[$id] ?? null;
	}

	public function getRequestsFrom(Player $player){
		$requests = [];
		foreach($this->requests as $id => $request){
			if($request->getRequester() == $player){
				$requests[] = $request;
			}
		}
		return $requests;
	}

	public function getRequestsTo(Player $player){
		$requests = [];
		foreach($this->requests as $id => $request){
			if($request->getTarget() == $player){
				$requests[] = $request;
			}
		}
		return $requests;
	}

	public function createRequest(Player $requester, Player $target){
		$arena = $this->plugin->getArena();
		$send = false;
		if(!$arena->inArena($target)) $send = true;

		$request = new Request($requester, $target, $send);
		$this->requests[$request->getId()] = $request;
	}


}