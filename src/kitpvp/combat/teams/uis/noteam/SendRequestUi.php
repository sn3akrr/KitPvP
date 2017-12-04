<?php namespace kitpvp\combat\teams\uis\noteam;

use pocketmine\{
	Player,
	Server
};
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\CustomForm;
use core\ui\elements\customForm\{
	Label,
	Input
};

class SendRequestUi extends CustomForm{

	public $player;

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("Send Team Request");

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$this->addElement(new Label("Type part of the player's name that you're trying to team with, then tap submit. Leave blank to go back."));
		$this->addElement(new Input("Player name", "m4l0ne23"));		
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if($teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are already in a team!");
			return;
		}
		$name = $response[1];
		if($name == ""){
			$player->showModal(new NoTeamMainUi($player));
			return;
		}
		$p = Server::getInstance()->getPlayer($name);
		if(!$p instanceof Player){
			$player->sendMessage(TextFormat::RED . "The player you tried to send a team request to is not online.");
			return;
		}
		if($p == $player){
			$player->sendMessage(TextFormat::RED . "You cannot send a team request to yourself.");
			return;
		}
		if($teams->inTeam($p)){
			$player->sendMessage(TextFormat::RED . $p->getName() . " is already in a team!");
			return;
		}
		$teams->createRequest($player, $p);
	}

}