<?php namespace kitpvp\duels\uis;
//TODO: REWRITE SO IT ISNT HARDCODED BULLSHIT!

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;

class QueueSelectUi extends SimpleForm{

	public $queues = [];

	public function __construct(Player $player){
		parent::__construct("Duels", "Choose an option below.");

		$this->queues[] = $queue = KitPvP::getInstance()->getDuels()->queues["random"];

		$this->addButton(new Button("Random Duel" . PHP_EOL .
			($queue->inQueue($player) ? "Tap to leave queue" : "Tap to join queue")
		));
		$this->addButton(new Button("Select preferred map" . PHP_EOL . ($player->getRank() == "default" ? "Requires a rank!" : "")));
	}

	public function handle($response, Player $player){
		if(KitPvP::getInstance()->getDuels()->inDuel($player)){
			$player->sendMessage(TextFormat::RED . "You cannot use this menu while in a duel!");
			return;
		}
		if($response == 0){
			if(!$this->queues[0]->inQueue($player)){
				$this->queues[0]->addPlayer($player);
				$player->sendMessage(TextFormat::GREEN . "You have joined the duel queue!");
				return;
			}
			$this->queues[0]->removePlayer($player);
			$player->sendMessage(TextFormat::GREEN . "Left duel queue.");
			return;
		}
		if($player->getRank() == "default"){
			$player->sendMessage(TextFormat::RED . "This feature requires a premium rank! Purchase one at " . TextFormat::YELLOW . Links::SHOP);
			return;
		}
		$player->showModal(new PreferredMapUi());
	}

}