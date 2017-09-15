<?php namespace kitpvp\kits\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\kits\KitObject;
use kitpvp\KitPvP;

use core\ui\windows\CustomForm;
use core\ui\elements\customForm\Label;

class KitConfirmUi extends CustomForm{

	public $kit;

	public function __construct(KitObject $kit, Player $player){
		$this->kit = $kit;

		parent::__construct("Confirm");
		$this->addElement(new Label("Scroll to view kit details\n\n".$kit->getVisualItemList()."\n\n".(KitPvP::getInstance()->getKits()->hasKitPassActive($player) ? TextFormat::LIGHT_PURPLE."You have a Kit Pass active, so the next kit you equip will not charge you.".TextFormat::RESET."\n\n" : "")."Press Submit to equip this kit."));
	}

	public function handle($response, Player $player){
		$this->kit->equip($player);
		$player->sendMessage(TextFormat::AQUA."Kits> ".TextFormat::GREEN."You equipped the ".$this->kit->getName()." kit!");
	}

}