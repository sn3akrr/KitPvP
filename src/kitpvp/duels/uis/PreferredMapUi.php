<?php namespace kitpvp\duels\uis;

use pocketmine\utils\TextFormat;
use pocketmine\Player;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;

class PreferredMapUi extends SimpleForm{

	public $arenas = [];

	public function __construct(){
		parent::__construct("Select Map", "Select which map you'd like to duel in next round.");

		$arenas = KitPvP::getInstance()->getDuels()->getArenas();
		$key = 0;
		foreach($arenas as $name => $arena){
			$this->arenas[$key] = $arena;
			$key++;
			$this->addButton(new Button($arena->getName() . PHP_EOL . "Tap to select map"));
		}
		$this->addButton(new Button("Go back"));
	}

	public function handle($response, Player $player){
		if(KitPvP::getInstance()->getDuels()->inDuel($player)){
			$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You cannot use this menu while in a duel!");
			return;
		}
		foreach($this->arenas as $key => $arena){
			if($key == $response){
				KitPvP::getInstance()->getDuels()->setPreferredMap($player, $arena->getId());
				$player->sendMessage(TextFormat::YELLOW . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Set preferred arena to " . TextFormat::LIGHT_PURPLE . $arena->getName() . TextFormat::GRAY . ". Your next duel will take place in this map.");
			}
		}
		$player->showModal(new QueueSelectUi($player));
	}

}