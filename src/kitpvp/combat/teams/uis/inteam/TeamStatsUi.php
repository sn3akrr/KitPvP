<?php namespace kitpvp\combat\teams\uis\inteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class TeamStatsUi extends SimpleForm{

	public $player;

	public function __construct(Player $player){
		$this->player = $player;

		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		$team = $teams->getPlayerTeam($player);

		parent::__construct("Team Stats",
			"Kills: " . $team->getKills() . PHP_EOL . 
			"Deaths: " . $team->getDeaths() . PHP_EOL . 
			"KDR: " . $team->getKdr()
		);

		$this->addButton(new Button("Exit"));
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if(!$teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are not in a team!");
			return;
		}
		$player->showModal(new InTeamMainUi($player));
	}

}