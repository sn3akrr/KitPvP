<?php namespace kitpvp\combat\teams\uis\noteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class NoTeamMainUi extends SimpleForm{

	public $player;

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("Teams", "Select an option.");

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$tocount = count($teams->getRequestsTo($player));
		$fromcount = count($teams->getRequestsFrom($player));

		$this->addButton(new Button("Send Team Request"));
		$this->addButton(new Button("View requests received" . PHP_EOL . "(" . $tocount . ")"));
		$this->addButton(new Button("View requests sent" . PHP_EOL . "(" . $fromcount . ")"));
		$this->addButton(new Button("Exit"));
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if($teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are already in a team!");
			return;
		}
		if($response == 0){
			$player->showModal(new SendRequestUi($player));
			return;
		}
		if($response == 1){
			$player->showModal(new PendingToUi($player));
			return;
		}
		if($response == 2){
			$player->showModal(new PendingFromUi($player));
			return;
		}
	}

}