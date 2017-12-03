<?php namespace kitpvp\combat\teams\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;
use kitpvp\combat\team\Request;

use core\ui\windows\ModalWindow;

class TeamRequestUi extends ModalWindow{

	public $request;

	public function __construct(Request $request){
		$this->request = $request;
	}

	public function onClose(Player $player){
		if(!$this->request->isClosed()){
			$this->request->deny();
			return;
		}
	}

	public function handle($response, Player $player){
		$teams = KitPvP::getInstance()->getCombat()->getTeams();
		if($this->request->isClosed()){
			$player->sendMessage(TextFormat::RED . "This team request has expired!");
			return;
		}
		if($teams->inTeam($player)){
			$player->sendMessage(TextFormat::RED . "You are already in a team!");
		}
		if($response){
			$this->request->accept();
		}else{
			$this->request->deny();
		}
	}

}