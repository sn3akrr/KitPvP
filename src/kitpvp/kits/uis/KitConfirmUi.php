<?php namespace kitpvp\kits\uis;

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use kitpvp\kits\KitObject;
use kitpvp\KitPvP;

use core\ui\windows\CustomForm;
use core\ui\elements\customForm\{
	Label,
	Toggle
};

use core\network\Links;

class KitConfirmUi extends CustomForm{

	public $kit;
	public $session;

	public function __construct(KitObject $kit, Player $player){
		$this->kit = $kit;
		$this->session = $session = KitPvP::getInstance()->getKits()->getSession($player);

		parent::__construct("Kit Details");
		$this->addElement(new Label("Scroll to view kit details\n\n".$kit->getVisualItemList()."\n\nPress Submit to equip this kit."));
		$this->addElement(new Toggle("Use Free Play (" . $session->getFreePlays($kit) . ")"));
	}

	public function handle($response, Player $player){
		$session = KitPvP::getInstance()->getKits()->getSession($player);
		$kit = $this->kit;
		if($session->hasKit()){
			$player->sendMessage(TextFormat::RED."You already have a kit equipped!");
			return;
		}
		$freeplay = $response[1];
		if($freeplay){
			if($session->getFreePlays($kit) <= 0){
				$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(!) " . TextFormat::RESET . TextFormat::GRAY . "You do not have any free plays! Collect them in Supply Drops, Mystery Boxes, or purchase them at ".TextFormat::YELLOW.Links::SHOP);
				return;
			}
			$session->takeFreePlays($kit);
			$kit->equip($player);
			$player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Used " . TextFormat::AQUA . "x1 Free Play " . TextFormat::GRAY . "to equip the " . TextFormat::YELLOW . $kit->getName() . TextFormat::GRAY . " kit.");
			return;
		}
		if(!$kit->hasRequiredRank($player)){
			$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You must be at least rank " . TextFormat::YELLOW . TextFormat::BOLD . strtoupper($kit->getRequiredRank()) . TextFormat::RESET . TextFormat::GRAY . " to purchase this kit! Purchase this rank at ".TextFormat::YELLOW.Links::SHOP);
			return;
		}
		if(!$kit->hasEnoughCurrency($player)){
			$player->sendMessage(TextFormat::RED . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "You do not have enough Techits to purchase this kit! (".$kit->getPrice().")");
			return;
		}
		$kit->purchase($player);
		$player->sendMessage(TextFormat::GREEN . TextFormat::BOLD . "(i) " . TextFormat::RESET . TextFormat::GRAY . "Successfully purchased the ". TextFormat::YELLOW . $kit->getName() . TextFormat::GRAY ." kit!");
	}

}