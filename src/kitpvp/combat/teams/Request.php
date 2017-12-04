<?php namespace kitpvp\combat\teams;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use kitpvp\combat\teams\uis\TeamRequestUi;

class Request{

	const TIMEOUT = 60;

	public $id;
	public $createdtime;

	public $requester;
	public $target;

	public $closed = false;

	public function __construct(Player $requester, Player $target, $sendmenu){
		$this->requester = $requester;
		$this->target = $target;

		$this->id = Teams::$requestCount++;
		$this->createdtime = time();

		$requester->sendMessage(TextFormat::GREEN . "Sent a team request to " . $target->getName());
		$target->sendMessage(TextFormat::GREEN . "Received a team request from " . $requester->getName() . ". " . ($sendmenu ? "" : "Please check your team menu with /team to accept or deny the request."));
		if($sendmenu){
			$target->showModal(new TeamRequestUi($this));
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getCreatedTime(){
		return $this->createdtime;
	}

	public function getRequester(){
		return $this->requester;
	}

	public function getTarget(){
		return $this->target;
	}

	public function isClosed(){
		return $this->closed;
	}

	public function isFrom(Player $player){
		return $player == $this->getRequester();
	}

	public function isTo(Player $player){
		return $player == $this->getTarget();
	}

	final public function accept(){
		$this->close();

		KitPvP::getInstance()->getCombat()->getTeams()->createTeam($this->getRequester(), $this->getTarget());
	}

	final public function deny($sendMessageToRequester = true, $sendMessageToTarget = true){
		$this->close();

		if($sendMessageToRequester){
			$this->getRequester()->sendMessage(TextFormat::RED . $this->getTarget()->getName() . " denied your team request!");
		}
		if($sendMessageToTarget){
			$this->getTarget()->sendMessage(TextFormat::GREEN . "Denied team request from " . $this->getRequester()->getName());
		}
	}

	final public function cancel(){
		$this->getRequester()->sendMessage(TextFormat::GREEN . "Cancelled team request to ".$this->getTarget()->getName());
		$this->getTarget()->sendMessage(TextFormat::RED . "Team request from " . $this->getRequester()->getName() . " was cancelled.");

		$this->close();
	}

	public function canTimeout(){
		return ($this->getCreatedTime() + self::TIMEOUT) - time() <= 0 || $this->getRequester()->isClosed() || $this->getTarget()->isClosed();
	}

	public function timeout(){
		if(!$this->getRequester()->isClosed()){
			$this->getRequester()->sendMessage(TextFormat::RED . "Team request to " . $this->getTarget()->getName() . " timed out.");
		}
		if(!$this->getTarget()->isClosed()){
			$this->getTarget()->sendMessage(TextFormat::RED . "Team request from " . $this->getRequester()->getName() . " timed out.");
		}

		$this->close();
	}

	final public function close(){
		$this->closed = true;

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		unset($teams->requests[$this->getId()]);
	}

}