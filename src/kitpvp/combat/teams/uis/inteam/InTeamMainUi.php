<?php namespace kitpvp\combat\teams\uis\inteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class InTeamMainUi extends SimpleForm{

	public $player;

	public function __construct(Player $player){
		$this->player = $player;
		parent::__construct("Teams", "Select an option.");

		$this->addButton(new Button("Team Stats"));
		$this->addButton(new Button("Disband team"));
		$this->addButton(new Button("Exit"));
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if(!$teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are not in a team!");
			return;
		}
		if($response == 0){
			$player->showModal(new TeamStatsUi($player));
			return;
		}
		if($response == 1){
			$player->showModal(new DisbandTeamUi($player, $teams->getPlayerTeam($player)));
			return;
		}
	}

}