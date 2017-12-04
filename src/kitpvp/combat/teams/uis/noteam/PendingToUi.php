<?php namespace kitpvp\combat\teams\uis\noteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class PendingToUi extends SimpleForm{

	public $player;
	public $requests = [];

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("Received Requests", "Tap on a request to accept it.");

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$requests = $teams->getRequestsTo($player);
		$key = 0;
		foreach($requests as $request){
			$this->requests[$key] = $request;
			$key++;

			$this->addButton(new Button("From: " . $request->getRequester()->getName() . PHP_EOL . "Tap to accept"));
		}

		$this->addButton(new Button("Exit"));
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if($teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are already in a team!");
			return;
		}
		foreach($this->requests as $id => $request){
			if($id == $response){
				if($request->isClosed()){
					$player->sendMessage(TextFormat::RED . "This request has expired.");
					return;
				}
				$request->accept();
				return;
			}
		}
		$player->showModal(new NoTeamMainUi($player));
	}

}