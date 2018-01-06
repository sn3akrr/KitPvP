<?php namespace kitpvp\arena\spectate\uis;

use pocketmine\{
	Player,
	Server
};

use kitpvp\KitPvP;
use kitpvp\kits\uis\KitSelectUi;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class SpectateChooseUi extends SimpleForm{

	public function __construct(){
		parent::__construct("Spectate", "What would you like to do?");
		$this->addButton(new Button("Keep Spectating"));
		$this->addButton(new Button("Fight again!"));
		$this->addButton(new Button("Exit Arena"));
	}

	public function handle($response, Player $player){
		if($response == 0) return;

		if($response == 1){
			$player->showModal(new KitSelectUi($player));
			return;
		}
		if($response == 2){
			KitPvP::getInstance()->getArena()->exitArena($player);
			return;
		}
	}

}