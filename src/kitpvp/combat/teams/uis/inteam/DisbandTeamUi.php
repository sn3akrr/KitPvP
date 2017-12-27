<?php namespace kitpvp\combat\teams\uis\inteam;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use kitpvp\combat\teams\Team;

use core\ui\windows\ModalWindow;

use kitpvp\KitPvP;

class DisbandTeamUi extends ModalWindow{

	public $team;

	public function __construct(Player $player, Team $team){
		$this->team = $team;
		parent::__construct("Disband Team", "Are you sure you want to disband your team with " . TextFormat::GREEN . $team->getOppositeMember($player)->getName() . TextFormat::WHITE . "?", "Disband", "Cancel");
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if(!$teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are not in a team!");
			return;
		}
		if($response){
			$this->team->disband($player->getName() . " left");
			return;
			$as = KitPvP::getInstance()->getAchievements()->getSession($player);
			if(!$as->hasAchievement("team_2")){
				$as->get("team_2");
			}
			return;
		}
		$player->showModal(new InTeamMainUi($player));
	}

}