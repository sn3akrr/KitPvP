<?php namespace kitpvp\kits\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\KitPvP;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

class KitSelectUi extends SimpleForm{

	public $kits = [];

	public function __construct(Player $player){
		parent::__construct("Kit Selector", "Which kit would you like? You have " . $player->getTechits() . " techits available.");
		$kits = KitPvP::getInstance()->getKits();

		$session = $kits->getSession($player);

		foreach($kits->getKitList() as $kit){
			$kit = $kits->getKit($kit);
			$this->kits[] = $kit;
			$this->addButton(new Button($kit->getName() . PHP_EOL . $kit->getPrice() . " techits"));
		}
	}

	public function handle($response, Player $player){
		$kits = KitPvP::getInstance()->getKits();
		$session = $kits->getSession($player);

		if($session->hasKit()){
			$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "You already have a kit equipped!");
			return;
		}
		foreach($this->kits as $key => $kit){
			if($key == $response){
				$player->showModal(new KitConfirmUi($kit, $player));
				return;
			}
		}
	}

}