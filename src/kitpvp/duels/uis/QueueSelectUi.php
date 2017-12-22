<?php namespace kitpvp\duels\uis;
//TODO: REWRITE SO IT ISNT HARDCODED BULLSHIT!

use pocketmine\Player;
use pocketmine\utils\TextFormat;

use core\ui\windows\SimpleForm;
use core\ui\elements\simpleForm\Button;

use kitpvp\KitPvP;

use core\network\Links;

class QueueSelectUi extends SimpleForm{

	public $queues = [];

	public function __construct(Player $player){
		parent::__construct("Duels", "Choose an option below.");

		$queues = KitPvP::getInstance()->getDuels()->getQueues();
		$key = 0;
		foreach($queues as $id => $queue){
			$this->queues[$key] = $queue;
			$key++;
			$this->addButton(new Button($queue->getName() . PHP_EOL .
				($queue->inQueue($player) ? "Tap to leave queue" : "Tap to enter queue")
			));
		}
		$this->addButton(new Button("Select a map"));
	}

	public function handle($response, Player $player){
		$duels = KitPvP::getInstance()->getDuels();
		if($duels->inDuel($player)){
			$player->sendMessage(TextFormat::RED . "You cannot use this menu while in a duel!");
			return;
		}
		foreach($this->queues as $key => $queue){
			if($response == $key){
				if(!$queue->inQueue($player)){
					$queue->addPlayer($player, $duels->getPreferredMap($player));
					$player->sendMessage(TextFormat::GREEN . "You have joined the '" . $queue->getName() . "' queue!");
					return;
				}
				$queue->removePlayer($player);
				$player->sendMessage(TextFormat::GREEN . "Left '" . $queue->getName() . "' queue.");
				return;
			}
		}
		if($response == count($this->queues)){
			if($player->getRank() == "default"){
				$player->sendMessage(TextFormat::RED . "This feature requires a premium rank! Purchase one at " . TextFormat::YELLOW . Links::SHOP);
				return;
			}
			$player->showModal(new PreferredMapUi());
			return;
		}
	}

}