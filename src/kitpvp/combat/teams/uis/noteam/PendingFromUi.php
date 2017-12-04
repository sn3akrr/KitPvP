<?php namespace kitpvp\combat\teams\uis\noteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class PendingFromUi extends SimpleForm{

	public $player;
	public $requests = [];

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("Sent Requests", "Tap on a request to cancel it.");

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$requests = $teams->getRequestsFrom($player);
		$key = 0;
		foreach($requests as $request){
			$this->requests[$key] = $request;
			$key++;

			$this->addButton(new Button("To: " . $request->getTarget()->getName() . PHP_EOL . "Tap to cancel"));
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
				$request->cancel();
				return;
			}
		}
		$player->showModal(new NoTeamMainUi($player));
	}

}