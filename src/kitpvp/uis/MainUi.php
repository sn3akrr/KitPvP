<?php namespace kitpvp\uis;

use pocketmine\Player;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;

class MainUi extends SimpleForm{

	public $player;

	public function __construct(Player $player){
		$this->player = $player;

		parent::__construct($player->getName() . "'s menu", "(WIP Menu)");

		$this->addButton(new Button("View Stats"));
		$this->addButton(new Button("Send Duel Request"));
		$this->addButton(new Button("Send Team Request"));
		//$this->addButton(new Button("Place bounty"));
	}

	public function handle($response, Player $player){

	}

}